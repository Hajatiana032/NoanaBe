<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_user')]
class UserController extends AbstractController
{
    #[Route('/utilisateurs', name: '')]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'currentMenu' => 'adminUser',
            'controller_name' => 'UserController',
        ]);
    }
}
