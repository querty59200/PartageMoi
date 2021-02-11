<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
        ]);
    }

    /**
     * @Route("/rgpd", name="app_rgpd")
     */
    public function rgpd(Request $request): Response
    {
        return $this->render('app/modal/rgpd.html.twig', [
        ]);
    }

    /**
     * @Route("/qui-sommes-nous", name="app_who_are_we")
     */
    public function whoAreWe(Request $request): Response
    {
        return $this->render('app/modal/qui_sommes_nous.html.twig', [
        ]);
    }
}
