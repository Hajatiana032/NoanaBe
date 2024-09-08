<?php

namespace App\Controller\admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/produits', name: 'admin_product')]
final class ProductController extends AbstractController
{
    /**
     * todo Get all products
     *
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route(name: '', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'currentMenu' => 'admin_product',
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ? add a select for the new product
            $product->setSlug($slugger->slug($product->getTitle())->lower());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/product/new.html.twig', [
            'currentMenu' => 'admin_product',
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: '_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'currentMenu' => 'admin_product',
            'product' => $product,
        ]);
    }

    #[Route('/modification/{slug}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['slug' => 'slug'])] Product $product, EntityManagerInterface $entityManager): Response
    {
        $currentProduct = $product->getTitle();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', "Le produit {$currentProduct}  a été modifié.");

            return $this->redirectToRoute('admin_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/product/edit.html.twig', [
            'currentMenu' => 'admin_product',
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/suppression/{slug}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['slug' => 'slug'])] Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('danger', "Le produit {$product->getTitle()} a été supprimé.");
        }

        return $this->redirectToRoute('admin_product', [], Response::HTTP_SEE_OTHER);
    }
}
