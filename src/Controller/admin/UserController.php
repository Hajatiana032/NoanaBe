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
#[Route('/admin/utilisateurs', name: 'admin_user')]
class UserController extends AbstractController
{
    /**
     * todo Get all users
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('', name: '')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'currentMenu' => 'admin_user',
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * todo Edit the user role
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    #[Route('/roles/super_admin:{admin}/utilisateur:{slug}', name: '_roles')]
    public function editUserRoles(EntityManagerInterface $em, #[MapEntity(mapping: ['slug' => 'slug'])] User $user, Request $request): Response
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

    /**
     * todo Delete an user
     */
    #[Route('/suppression/{slug}', name: '_delete')]
    public function delete(#[MapEntity(mapping: ['slug' => 'slug'])] User $user, EntityManagerInterface $em): Response
    {
        $currentRoles = $user->getRoles();
        $count = count($em->getRepository(User::class)->findByRole("ROLE_SUPER_ADMIN"));
        if (in_array("ROLE_SUPER_ADMIN", $currentRoles) && $count <= 1) {
            $this->addFlash('danger', "Vous ne pouvez pas supprimer le dernier super administrateur.");
            return $this->redirectToRoute('admin_user');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('danger', "L' utilisateur(rice) <span class='text-capitalize'>{$user->getFirstname()}</span> <span class='text-uppercase'>{$user->getLastname()}</span> a été supprimé(e).");

        return $this->redirectToRoute('admin_user');
    }

    /**
     * todo Search an user
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return void
     */
    #[Route('?recherche', name: '_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository)
    {
        return $this->render('admin/user/search_results.html.twig', [
            'currentMenu' => 'admin_user',
            'query' => $request->query->get('q'),
            'users' => $userRepository->search($request->query->get('q'))
        ]);
    }
}
