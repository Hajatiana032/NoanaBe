<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    /**
     * todo Show the products of the selected category
     */
    #[Route('/catégorie/{slug}-{id}', name: 'app_category')]
    public function index(Category $category, ProductRepository $productRepository, PaginationService $pagination, Request $request): Response
    {
        return $this->render('category/index.html.twig', [
            'currentMenu' => 'category',
            'category' => $category,
            'products' => $pagination->paginate($productRepository->findBy(['category' => $category]), $request)
        ]);
    }
}
