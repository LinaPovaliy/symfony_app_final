<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Entity\Category;
use App\Form\CommentType;
use App\Message\AdvertisementMessage;
use App\Repository\AdvertisementRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface    $bus,
    )
    {
    }

    #[Route('/', name: 'homepage')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/{slug}', name: 'category')]
    public function show(Request $request, Category $category, AdvertisementRepository $advertisementRepository, CategoryRepository $categoryRepository): Response
    {
        $advertisement = new Advertisement();
        $form = $this->createForm(CommentType::class, $advertisement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $advertisement->setCategory($category);

            $this->entityManager->persist($advertisement);
            $this->entityManager->flush();
            $this->bus->dispatch(new AdvertisementMessage($advertisement->getId()));

            return $this->redirectToRoute('category', ['slug' => $category->getSlug()]);
        }


        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $advertisementRepository->getAdvertisementPaginator($category, $offset);

        return $this->render('category/show.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'category' => $category,
            'advertisements' => $paginator,
            'previous' => $offset - AdvertisementRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AdvertisementRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form,
        ]);
    }

    #[Route('/api/order/{id}', name: 'api_get_advertisement', methods: ['GET'])]
    public function getAdvertisement($id, AdvertisementRepository $advertisementRepository): JsonResponse
    {
        $advertisement = $advertisementRepository->find($id);

        if (!$advertisement) {
            return new JsonResponse(['error' => 'Advertisement not found'], 404);
        }

        $data = [
            'name' => $advertisement->getName(),
            'category' => [
                'name' => $advertisement->getCategory()->getName(),
            ],
            'status' => $advertisement->getStatus(),
            'hash' => $advertisement->getHash(),
            'created_at' => $advertisement->getCreatedAt()->format('Y-m-d H:i:s'),
            'public_url' => $this->generateUrl('category', [
                'slug' => $advertisement->getCategory()->getSlug(),
                'order_hash' => $advertisement->getHash(),
            ], true),
        ];

        return new JsonResponse($data);
    }
}