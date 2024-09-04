<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('/utilisateurs', name: '')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'currentMenu' => 'adminUser',
            'controller_name' => 'UserController',
        ]);
    }
}
