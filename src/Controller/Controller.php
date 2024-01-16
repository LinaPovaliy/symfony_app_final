<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\AdvertisementRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/{id}', name: 'category')]
    public function show(Request $request, Category $category, AdvertisementRepository $advertisementRepository, CategoryRepository $categoryRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $advertisementRepository->getAdvertisementPaginator($category, $offset);

        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'category' => $category,
            'advertisements' => $paginator,
            'previous' => $offset - AdvertisementRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AdvertisementRepository::PAGINATOR_PER_PAGE),
        ]);
    }
}