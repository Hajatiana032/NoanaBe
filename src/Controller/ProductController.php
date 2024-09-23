<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/{slug}-{id}', name: 'product_show')]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        return $this->render('product/show.html.twig', [
            'productsLike' => $productRepository->like($product->getCategory()->getId(), $product->getId()),
            'product' => $product
        ]);
    }
}
