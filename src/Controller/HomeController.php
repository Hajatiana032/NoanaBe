<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(ProductRepository $productRepository, PaginationService $pagination, Request $request): Response
    {
        return $this->render('home/index.html.twig', [
            'currentMenu' => 'home',
            'products' => $pagination->paginate($productRepository->findAll(), $request)
        ]);
    }
}
