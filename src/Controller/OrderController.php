<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\CityRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {

        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('order/index.html.twig', []);
    }

    #[Route('/commande/frais_de_livraison/{id}', methods: ['GET'])]
    public function getShippingPrice(City $city, ProductRepository $productRepository, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);

        $data = [];
        $totalPrice = 0;
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            $data[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
            $totalPrice += $product->getPrice() * $quantity;
        }

        return new JsonResponse(['shippingCost' => $city->getShippingCost(), 'totalPrice' => $totalPrice], JsonResponse::HTTP_OK);
    }
}
