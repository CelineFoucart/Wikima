<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Form\AdvancedSearchType;
use App\Form\SearchType;
use App\Repository\ImageRepository;
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
    public function gallery(Request $request, int $perPageEven): Response
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
}
