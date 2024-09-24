<?php

namespace App\EventSubscriber;

use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CategorySubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig, private CategoryRepository $categoryRepository) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $category = $this->categoryRepository->findAll();

        $this->twig->addGlobal('categories', $category);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
