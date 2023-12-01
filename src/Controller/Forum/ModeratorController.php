<?php

namespace App\Controller\Forum;

use App\Entity\Post;
use App\Entity\Report;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\TopicType;
use App\Repository\PostRepository;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

#[Route('/moderator-panel')]
class ModeratorController extends AbstractController
{
    public function __construct(bool $enableForum, private EntityManagerInterface $em)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'app_moderation_home')]
    public function indexAction(ReportRepository $reportRepository): Response
    {
        return $this->render('forum/moderation/report.html.twig', [
            'reports' => $reportRepository->findAll(),
        ]);
    }

    #[Route('/topic/{id}', name: 'app_moderation_topic')]
    public function topicAction(Topic $topic, Request $request, PostRepository $postRepository): Response
    {
        $post = $postRepository->findFirstPost($topic->getId());
        $post = (empty($post)) ? null : $post[0];

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $topic->setUpdatedAt(new \DateTime());
            $this->em->persist($topic);
            $this->em->flush();

            return $this->redirectToRoute('app_moderation_topic', ['id' => $topic->getId()]);
        }

        return $this->render('forum/moderation/topic.html.twig', [
            'topic' => $topic,
            'form' => $form,
            'post' => $post,
        ]);
    }

    #[Route('/topic/{id}/delete', name: 'app_moderation_topic_delete')]
    public function deleteTopicAction(Topic $topic, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$topic->getId(), $request->request->get('_token'))) {
            $this->em->remove($topic);
            $this->em->flush();
            $this->addFlash('success', "Le sujet a été supprimé avec succès.");
        }

        return $this->redirectToRoute('app_moderation_home');
    }

    #[Route('/post/{id}', name: 'app_moderation_post')]
    public function postAction(Post $post, Request $request): Response
    {
        $form = $this->getAuthorForm($post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $post->setUpdatedAt(new \DateTime());
            $this->em->persist($post);
            $this->em->flush();

            return $this->redirectToRoute('app_moderation_post', ['id' => $post->getId()]);
        }

        return $this->render('forum/moderation/post.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/report/{id}', name: 'app_moderation_delete', methods:['POST'])]
    public function deleteReportAction(Report $report, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $this->em->remove($report);
            $this->em->flush();
            $this->addFlash('success', "Le rapport a été supprimé avec succès.");
        }

        return $this->redirectToRoute('app_moderation_home');
    }

    /**
     * @param Post|Topic $entity
     * 
     * @return FormInterface
     */
    private function getAuthorForm(mixed $entity): FormInterface
    {
        return  $this->createFormBuilder($entity)
            ->add('author', EntityType::class, ['class' => User::class,'required' => true])
            ->getForm()
        ;
    }
}