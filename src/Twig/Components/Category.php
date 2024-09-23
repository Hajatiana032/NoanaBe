<?php

namespace App\Twig\Components;

use App\Repository\CategoryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
class Category
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    public function getCategories()
    {
        return $this->categoryRepository->findAll();
    }
}
