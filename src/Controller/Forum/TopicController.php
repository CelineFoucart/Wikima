<?php

namespace App\Controller\Forum;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;
use App\Entity\Topic;
use App\Entity\Report;
use App\Form\PostType;
use DateTimeImmutable;
use App\Form\ReportType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Security\Voter\VoterHelper;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/forum')]
class TopicController extends AbstractController
{
    public function __construct(bool $enableForum, private EntityManagerInterface $em)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/topic-{slug}', name: 'app_forum_topic_show')]
    public function topic(Topic $topic, PostRepository $postRepository, Request $request, int $perPageOdd): Response
    {
        $post = (new Post())
            ->setTopic($topic)
            ->setTitle('Re: ' . $topic->getTitle())
            ->setContent($this->getReplyTo($postRepository, $request))
        ;

        $page = $request->query->getInt('page', 1);
        $posts = $postRepository->findPaginated($topic->getId(), $page, $perPageOdd);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->getUser() instanceof User) { 
            $this->denyAccessUnlessGranted(VoterHelper::CREATE, $topic, 'Access Denied.');

            $post->setCreatedAt(new DateTimeImmutable())->setAuthor($this->getUser());
            $this->em->persist($post);
            $this->em->flush();

            $totalCurrent = count($posts);
            if ($totalCurrent < $perPageOdd) {
                $route = $this->generateUrl('app_forum_topic_show', ['slug' => $topic->getSlug(), 'page' => $page]);
            } else {
                $route = $this->generateUrl('app_forum_topic_show', ['slug' => $topic->getSlug(), 'page' => $page+1]);
            }
            $id = '#post'.$post->getId();

            return $this->redirect($route.$id);
        }

        $firstPost = $postRepository->findFirstPost($post->getTopic()->getId());
        $firstPostId = null;

        if (!empty($firstPost)) {
            $firstPostId = $firstPost[0]->getId();
        }

        return $this->render('forum/topic/topic.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'form' => $form,
            'page' => $page,
            'firstPostId' => $firstPostId,
        ]);
    }

    #[Route('/forum-{slug}/new', name: 'app_forum_topic_new')]
    #[IsGranted(new Expression("is_granted('ROLE_USER')"))]
    public function new(Forum $forum, Request $request, SluggerInterface $slugger, ValidatorInterface $validator): Response
    {
        $post = (new Post())->setAuthor($this->getUser());
        $errors = [];
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $created = new DateTimeImmutable();
            $topic = (new Topic())
                ->setTitle($post->getTitle())
                ->setSlug(strtolower($slugger->slug($post->getTitle())))
                ->setForum($forum)
                ->setLocked(false)
                ->setSticky(false)
                ->setCreatedAt($created)
                ->setAuthor($this->getUser());
            
            $violations = $validator->validate($topic);

            if (count($violations) > 0) {    
                $errors[] = "Ce titre est déjà utilisé";
            } else {
                $this->em->persist($topic);
                $post->setTopic($topic)->setCreatedAt($created);
                $this->em->persist($post);
                $this->em->flush();

                return $this->redirectToRoute('app_forum_topic_show', ['slug' => $topic->getSlug()]);
            }
        }

        return $this->render('forum/topic/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_edit')]
    #[IsGranted(new Expression("is_granted('ROLE_USER')"))]
    public function editPost(Post $post, Request $request, PostRepository $postRepository, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $post,'Access Denied.');

        $topicPage = $request->query->getInt('page', 1);
        $firstPost = $postRepository->findFirstPost($post->getTopic()->getId());
        $isFirstTopic = $firstPost[0]->getId() === $post->getId();

        $topic = $post->getTopic();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $post->setUpdatedAt(new DateTime());
            $this->em->persist($post);

            if ($isFirstTopic) {
                $topic->setTitle($post->getTitle())->setSlug(strtolower($slugger->slug($post->getTitle())));
                $this->em->persist($topic);
            }

            $this->em->flush();
            $this->addFlash('success', 'Le message a été édité.');

            $route = $this->generateUrl('app_forum_topic_show', ['slug' => $topic->getSlug(), 'page' => $topicPage]);

            return $this->redirect($route . '#post'.$post->getId());
        }

        return $this->render('forum/topic/edit.html.twig', [
            'form' => $form,
            'topic' => $topic,
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete')]
    #[IsGranted(new Expression("is_granted('ROLE_USER')"))]
    public function deleteAction(Post $post, Request $request, PostRepository $postRepository, LogService $logService): Response
    {
        $topic = $post->getTopic();
        $forum = $topic->getForum();
        $route = $this->generateUrl('app_forum_topic_show', ['slug' => $topic->getSlug()]);

        try {
            $this->denyAccessUnlessGranted(VoterHelper::DELETE, $post);
    
            if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
                $this->em->remove($post);
                $firstPost = $postRepository->findFirstPost($post->getTopic()->getId());
                $isFirstTopic = $firstPost[0]->getId() === $post->getId();

                if ($isFirstTopic) {
                    $this->em->remove($topic);
                    $route = $this->generateUrl('app_forum_forum_show', ['slug' => $forum->getSlug()]);
                }

                $this->em->flush();
                $this->addFlash('success', "Le post a été supprimé avec succès.");
            } else {
                $message = "La suppression a échoué car le token CSRF est invalide";
                $this->addFlash('error', $message);
                $logService->error("Suppression", $message . ' : ' . $post->getTitle() . '('. $post->getId() .')', 'Post');
            }
        } catch (AccessDeniedException $th) {
            $this->addFlash('error', "Vous ne pouvez pas supprimer ce message.");
            $logService->error("Suppression", "Tentative de suppression d'un message : " . $post->getTitle() . '('. $post->getId() .')', 'AccessDeniedException');
        }

        return $this->redirect($route);
    }

    #[Route('/post/{id}/report', name: 'app_post_report')]
    #[IsGranted(new Expression("is_granted('ROLE_USER')"))]
    public function reportAction(Post $post, Request $request): Response
    {
        $report = (new Report())->setAuthor($this->getUser())->setPost($post);
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $report->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($report);
            $this->em->flush();
            $this->addFlash('success', 'Votre rapport a bien été enregistré.');

            return $this->redirectToRoute('app_forum_topic_show', ["slug" => $post->getTopic()->getSlug()]);
        }

        return $this->render('forum/topic/report.html.twig', [
            'post' => $post,
            'form' => $form,
            'topic' => $post->getTopic(),
        ]);
    }

    private function getReplyTo(PostRepository $postRepository, Request $request): string
    {
        $replyTo = $request->query->getInt('reply', 0);

        if ($replyTo === 0) {
            return '';
        }

        $reply = $postRepository->find($replyTo);

        if ($reply === null) {
            return '';
        }

        $author = ($reply->getAuthor() !== null) ? '<p><strong>' . $reply->getAuthor()->getUsername() . ' a écrit :</strong></p> ' : '';

        return "<blockquote>{$author}{$reply->getContent()}</blockquote>";
    }
}
