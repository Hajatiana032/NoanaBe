<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    /**
     * todo Get all products
     *
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'currentMenu' => 'home',
            'products' => $productRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

    #[Route('/{slug}-{id}', name: 'app_show')]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        // dd($product->getCategory(), $product->getId());
        return $this->render('home/show.html.twig', [
            'currentMenu' => 'home',
            'productsLike' => $productRepository->like($product->getCategory()->getId(), $product->getId()),
            'product' => $product
        ]);
    }
}
