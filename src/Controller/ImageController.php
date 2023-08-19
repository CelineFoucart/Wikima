<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Entity\ImageTag;
use App\Form\AdvancedSearchType;
use App\Form\SearchType;
use App\Repository\ImageRepository;
use App\Repository\ImageTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }

    #[Route('/images', name: 'app_image_index')]
    public function gallery(Request $request, int $perPageEven, ImageTagRepository $imageTagRepository): Response
    {
        $search = (new SearchData())
            ->setPage($request->query->getInt('page', 1))
        ;
        $imageForm = $this->createForm(AdvancedSearchType::class, $search, ['allow_extra_fields' => true]);
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
            'types' => $imageTagRepository->findAll()
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
}
