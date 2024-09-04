<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/catégories', name: 'app_category')]
class CategoryController extends AbstractController
{
    /**
     * todo Get all categories
     *
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('', name: '')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * todo Add a category
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/ajout', name: '_new')]
    public function new(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ? add a slug for a category
            $category->setSlug($slugger->slug($category->getName())->lower());

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * todo Edit a category
     */
    #[Route('/modification/{slug}', name: '_edit')]
    public function edit(#[MapEntity(mapping: ['slug' => 'slug'])] Category $category, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ? edit the slug of selected category
            $category->setSlug($slugger->slug($category->getName())->lower());

            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form
        ]);
    }

    #[Route('/suppression/{slug}', name: '_delete')]
    public function delete(#[MapEntity(mapping: ['slug' => 'slug'])] Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('app_category');
    }
}
