<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Form\UtilisateurNewType;
use App\Form\UtilisateurTypeViaAdmi;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/utilisateurs", name="utilisateurs", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/utilisateur/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        \Swift_Mailer $mailer): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurTypeViaAdmi::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword(md5(uniqid()));
            $utilisateur->setActivationToken(md5(uniqid()));
            $entityManager->persist($utilisateur);
            $entityManager->flush();


            $message = (new \Swift_Message('Activation de votre compte - Partagez-moi'))
                ->setFrom('noreply@partagezmoi.fr')
                ->setTo($utilisateur->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/activation_admi.html.twig', ['token' => $utilisateur->getActivationToken()]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success' , 'Le compte a été crée. Un mail a été envoyé à l\'utilisateur');

            return $this->redirectToRoute('utilisateurs');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            $this->addFlash('success' , 'locataire supprimé avec succès');
        }

        return $this->redirectToRoute('utilisateurs');
    }

    /**
     * @Route("/activation/{token}", name="activation_gerant")
     */
    public function activation($token, UtilisateurRepository $utilisateurRepository)
    {
        $utilisateur = $utilisateurRepository->findOneBy(['activation_token' => $token]);

        if(!$utilisateur){
            // Error 404
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas');
        }

        // On supprime le token
        $utilisateur->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // on envoie un msg flash
        $this->addFlash('success' , 'Vous avez bien activé votre compte');

        // Redirection vers accueil
        return $this->redirectToRoute('home');
    }
}
