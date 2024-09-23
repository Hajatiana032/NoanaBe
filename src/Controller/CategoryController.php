<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/catégorie/{slug}-{id}', name: 'app_category')]
    public function index(Category $category): Response
    {
        return $this->render('category/index.html.twig', [
            'currentMenu' => 'category',
            'category' => $category
        ]);
    }
}
