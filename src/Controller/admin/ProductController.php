<?php

namespace App\Controller\admin;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

use function PHPUnit\Framework\throwException;
use function Zenstruck\Foundry\Persistence\persist;

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
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'currentMenu' => 'admin_product',
            // 'form' => $form,
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * todo Add a product
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/ajout', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ? add a select for the new product
            $product->setSlug($slugger->slug($product->getTitle())->lower());

            // ? add image for a product
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName =  $fileUploader->upload($imageFile);

                $product->setImage($imageFileName);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', "Le produit {$product->getTitle()} a été ajouté.");

            return $this->redirectToRoute('admin_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/product/new.html.twig', [
            'currentMenu' => 'admin_product',
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * todo Show a product
     */
    #[Route('/{slug}', name: '_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'currentMenu' => 'admin_product',
            'product' => $product,
        ]);
    }

    /**
     * todo Edit a product
     */
    #[Route('/modification/{slug}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['slug' => 'slug'])] Product $product, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $currentProduct = $product->getTitle();

        $oldImage = null;
        // ? Verify if the current product has an image
        if ($product->getImage()) {
            $oldImage = $this->getParameter('uploads_directory') . '/' . $product->getImage();
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ? add a slug for the new product
            $product->setSlug($slugger->slug($product->getTitle())->lower());

            // ? add image for a product
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }

                $imageFileName =  $fileUploader->upload($imageFile);
                $product->setImage($imageFileName);
            }

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

    /**
     * todo Delete a product
     */
    #[Route('/suppression/{slug}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['slug' => 'slug'])] Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            // ? Delete the product's image into the folder
            if (file_exists($this->getParameter('uploads_directory') . '/' . $product->getImage())) {
                unlink($this->getParameter('uploads_directory') . '/' . $product->getImage());
            }

            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('danger', "Le produit {$product->getTitle()} a été supprimé.");
        }

        return $this->redirectToRoute('admin_product', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * todo Search products
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return void
     */
    #[Route('?recherche', name: '_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository)
    {
        return $this->render('admin/product/search_results.html.twig', [
            'currentMenu' => 'admin_product',
            'query' => $request->query->get('q'),
            'products' => $productRepository->search($request->query->get('q'))
        ]);
    }
}
