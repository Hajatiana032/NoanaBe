<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/catégories', name: 'admin_category')]
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
        return $this->render('admin/category/index.html.twig', [
            'currentMenu' => 'admin_category',
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

            $this->addFlash('success', "La catégorie {$category->getName()} a été ajoutée.");

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/new.html.twig', [
            'currentMenu' => 'admin_category',
            'form' => $form
        ]);
    }

    /**
     * todo Edit a category
     */
    #[Route('/modification/{slug}', name: '_edit')]
    public function edit(#[MapEntity(mapping: ['slug' => 'slug'])] Category $category, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        // ? stock the old of the category into a variable
        $currentName = $category->getName();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($category->getName() == $currentName) {
                $this->addFlash('warning', "Aucun changement n' a été effectuer.");
                return $this->redirectToRoute('admin_category');
            }

            // ? edit the slug of selected category
            $category->setSlug($slugger->slug($category->getName())->lower());

            $em->flush();

            $this->addFlash('success', "La catégorie {$currentName} a été modifiée en {$category->getName()}.");

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'currentMenu' => 'admin_category',
            'category' => $category,
            'form' => $form
        ]);
    }

    /**
     * todo Delete a category
     */
    #[Route('/suppression/{slug}', name: '_delete')]
    public function delete(#[MapEntity(mapping: ['slug' => 'slug'])] Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash('danger', "La catégorie {$category->getName()} a été supprimée.");

        return $this->redirectToRoute('admin_category');
    }

    /**
     * todo Search a category
     *
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    #[Route('?recherche', name: '_search', methods: ['GET'])]
    public function search(Request $request, CategoryRepository $categoryRepository)
    {
        return $this->render('admin/category/search_results.html.twig', [
            'currentMenu' => 'admin_category',
            'query' => $request->query->get('q'),
            'categories' => $categoryRepository->search($request->query->get('q'))
        ]);
    }
}
