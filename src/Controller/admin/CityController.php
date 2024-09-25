<?php

namespace App\Controller\admin;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/ville', name: 'admin_city')]
#[IsGranted('ROLE_ADMIN')]
final class CityController extends AbstractController
{
    /**
     * todo Get all cities
     *
     * @param CityRepository $cityRepository
     * @return Response
     */
    #[Route(name: '', methods: ['GET'])]
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('admin/city/index.html.twig', [
            'currentMenu' => 'admin_city',
            'cities' => $cityRepository->findAll(),
        ]);
    }

    /**
     * todo Add a new city
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/ajout', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ? add a slug for the new city
            $city->setSlug($slugger->slug($city->getName())->lower());

            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', "La ville {$city->getName()} a été ajoutée.");

            return $this->redirectToRoute('admin_city', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/city/new.html.twig', [
            'currentMenu' => 'admin_city',
            'city' => $city,
            'form' => $form,
        ]);
    }

    /**
     * todo Edit the selected city
     */
    #[Route('/modification/{slug}-{id}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $currentCity = $city->getName();

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setSlug($slugger->slug($city->getName())->lower());
            $entityManager->flush();

            $this->addFlash('success', "La ville {$currentCity} a été modifiée.");

            return $this->redirectToRoute('admin_city', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    /**
     * todo Delete the selected city
     */
    #[Route('/suppression/{slug}-{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $city->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();

            $this->addFlash('danger', "La ville {$city->getName()} a été supprimée.");
        }

        return $this->redirectToRoute('admin_city', [], Response::HTTP_SEE_OTHER);
    }
}
