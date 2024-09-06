<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\RolesType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin', name: 'admin_user')]
class UserController extends AbstractController
{
    /**
     * todo Get all users
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/utilisateurs', name: '')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'currentMenu' => 'admin_user',
            'users' => $userRepository->findAll()
        ]);
    }

    #[IsGranted("ROLE_SUPER_ADMIN")]
    #[Route('/roles/super_admin:{admin}/utilisateur:{slug}', name: '_roles')]
    public function editUserRoles(EntityManagerInterface $em, #[MapEntity(mapping: ['slug' => 'slug'])] User $user, Request $request)
    {
        $currentRoles = $user->getRoles();

        $form = $this->createForm(RolesType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getRoles() == $currentRoles) {
                $this->addFlash('warning', "Aucun changement n' a été effectué.");
                return $this->redirectToRoute('admin_user');
            }

            $count = count($em->getRepository(User::class)->findByRole("ROLE_SUPER_ADMIN"));
            if (in_array("ROLE_SUPER_ADMIN", $currentRoles) && !in_array("ROLE_SUPER_ADMIN", $user->getRoles()) && $count <= 1) {
                $this->addFlash('danger', "Vous ne pouvez pas changer le role du dernier super administrateur.");
                return $this->redirectToRoute('admin_user');
            }

            $em->flush();
            $this->addFlash('success', "Le role a été modifié avec succès.");
            return $this->redirectToRoute('admin_user');
        }


        return $this->render('admin/user/editUserRoles.html.twig', [
            'currentMenu' => 'admin_user',
            'user' => $user,
            'form' => $form
        ]);
    }
}
