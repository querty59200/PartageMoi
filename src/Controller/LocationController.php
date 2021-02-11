<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/locations")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="locations", methods={"GET"})
     */
    public function index(): Response
    {
        $locations = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findAll();

        return $this->render('location/index.html.twig', [
            'locations' => $locations,
        ]);
    }

    /**
     * @Route("/new", name="location_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('locations');
        }

        return $this->render('location/new.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="location_show", methods={"GET"})
     */
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="location_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('locations');
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="location_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Location $location): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
            $this->addFlash('success', 'Location supprimée');
        }

        return $this->redirectToRoute('locations');
    }
}
