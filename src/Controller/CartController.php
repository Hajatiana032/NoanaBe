<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    /**
     * todo List of the product in cart
     *
     * @param ProductRepository $productRepository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/mon_panier', name: 'app_cart')]
    public function index(ProductRepository $productRepository, SessionInterface $session): Response
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

        return $this->render('cart/index.html.twig', [
            'currentMenu' => 'cart',
            'items' => $data,
            'total' => $totalPrice
        ]);
    }

    /**
     * todo Add an product into the cart but also increment the selected product
     */
    #[Route('/mon_panier/ajout/{slug}-{id}', name: 'app_cart_add')]
    public function add(SessionInterface $session, Product $product): Response
    {
        // ? get the product id
        $productId = $product->getId();

        // ? initialize the cart as an empty array
        $cart = $session->get('cart', []);

        // ? Check if product exists in the cart or else increment the quantity product
        if (empty($cart[$productId])) {
            $cart[$productId] = 1;
        } else {
            $cart[$productId]++;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * todo Decrement the quantity of the selected product
     */
    #[Route('/mon_panier/soustraction/{slug}-{id}', name: 'app_cart_remove')]
    public function remove(SessionInterface $session, Product $product): Response
    {
        // ? get the product id
        $productId = $product->getId();

        // ? initialize the cart as an empty array
        $cart = $session->get('cart', []);

        // ? Check if product exists in the cart and decrement if it is or else delete it from the cart
        if (!empty($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId]--;
            } else {
                unset($cart[$productId]);
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon_panier/suppression/{slug}-{id}', name: 'app_cart_delete')]
    public function delete(SessionInterface $session, Product $product): Response
    {
        // ? get the product id
        $productId = $product->getId();

        // ? initialize the cart as an empty array
        $cart = $session->get('cart', []);

        // ? Check if product exists in the cart and delete it from the cart
        if (!empty($cart[$productId])) {
            unset($cart[$productId]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon_panier/vider', name: 'app_cart_empty')]
    public function empty(SessionInterface $session): Response
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }
}
