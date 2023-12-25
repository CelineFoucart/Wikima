<?php

namespace App\Controller;

use App\Entity\ImageTag;
use App\Entity\ImageGroup;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\ImageRepository;
use App\Repository\ImageTagRepository;
use App\Repository\ImageGroupRepository;
use App\Form\Search\AdvancedImageSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }

    #[Route('/images', name: 'app_image_index')]
    public function gallery(Request $request, int $perPageEven): Response
    {
        $search = (new SearchData())
            ->setPage($request->query->getInt('page', 1))
        ;
        $imageForm = $this->createForm(AdvancedImageSearchType::class, $search, ['allow_extra_fields' => true]);
        $imageForm->handleRequest($request);
        
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $images = $this->imageRepository->search($search, [], $perPageEven);
        } else {
            $images = $this->imageRepository->findPaginated($search->getPage(), [], $perPageEven);
        }

        return $this->render('image/gallery.html.twig', [
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'images' => $images,
            'imageForm' => $imageForm->createView(),
        ]);
    }

    #[Route('/images/{slug}', name: 'app_image_show')]
    public function show(string $slug): Response
    {
        $image = $this->imageRepository->findBySlug($slug);

        if (null === $image) {
            throw $this->createNotFoundException();
        }

        return $this->render('image/show_image.html.twig', [
            'image' => $image,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/type/images/{slug}', name: 'app_image_type')]
    public function type(ImageTag $imageTag, Request $request, int $perPageOdd, ImageTagRepository $imageTagRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $images = $this->imageRepository->findByType($imageTag, $page, $perPageOdd);

        return $this->render('image/type.html.twig', [
            'images' => $images,
            'type' => $imageTag,
            'imageType' => $imageTag,
            'types' => $imageTagRepository->findAll(),
        ]);
    }

    #[Route('/image-group', name: 'app_image_group_index')]
    public function imageGroupIndex(ImageGroupRepository $imageGroupRepository): Response
    {
        return $this->render('image/group_index.html.twig', [
            'image_groups' => $imageGroupRepository->findBy([], ['title' => 'ASC']),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/image-group/{slug}', name: 'app_image_group_show')]
    public function imageGroupShow(ImageGroup $imageGroup): Response
    {
        return $this->render('image/group_show.html.twig', [
            'image_group' => $imageGroup,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
}
