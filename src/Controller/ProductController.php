<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\PaginationService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * todo Show the selected product
     */
    #[Route('/{slug}-{id}', name: 'product_show')]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        return $this->render('product/show.html.twig', [
            'productsLike' => $productRepository->like($product->getCategory()->getId(), $product->getId()),
            'product' => $product
        ]);
    }

    #[Route('?recherche', name: 'product_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository, PaginationService $pagination)
    {
        return $this->render('product/search_results.html.twig', [
            'query' => $request->query->get('q'),
            'products' => $pagination->paginate($productRepository->search($request->query->get('q')), $request, 15)
        ]);
    }
}
