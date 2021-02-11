<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Form\Pret1Type;
use App\Form\PretType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prets")
 */
class PretController extends AbstractController
{
    /**
     * @Route("/", name="prets", methods={"GET"})
     */
    public function index(): Response
    {
        $prets = $this->getDoctrine()
            ->getRepository(Pret::class)
            ->findAll();

        return $this->render('pret/index.html.twig', [
            'prets' => $prets,
        ]);
    }

    /**
     * @Route("/new", name="pret_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pret = new Pret();
        $form = $this->createForm(PretType::class, $pret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pret);
            $entityManager->flush();

            return $this->redirectToRoute('prets');
        }

        return $this->render('pret/new.html.twig', [
            'pret' => $pret,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pret_show", methods={"GET"})
     */
    public function show(Pret $pret): Response
    {
        return $this->render('pret/show.html.twig', [
            'pret' => $pret,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pret_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pret $pret): Response
    {
        $form = $this->createForm(PretType::class, $pret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prets');
        }

        return $this->render('pret/edit.html.twig', [
            'pret' => $pret,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pret_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pret $pret): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pret->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pret);
            $entityManager->flush();
        }

        return $this->redirectToRoute('prets');
    }
}
