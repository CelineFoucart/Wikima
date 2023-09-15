<?php

namespace App\Controller\Forum;

use App\Entity\Post;
use App\Entity\Forum;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $post = (new Post())->setTopic($topic)->setTitle('Re: ' . $topic->getTitle());
        $page = $request->query->getInt('page', 1);
        $posts = $postRepository->findPaginated($topic->getId(), $page, $perPageOdd);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->getUser() instanceof User) { 
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

        return $this->render('forum/topic/topic.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'form' => $form,
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

    // edit

    // delete post
}
