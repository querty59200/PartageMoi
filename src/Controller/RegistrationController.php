<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use App\Security\UtilisateurAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/enregistrement", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler,
                             UtilisateurAuthenticator $authenticator,
                             \Swift_Mailer $mailer) : Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );

            // On génère le token d'activation
            $utilisateur->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $message = (new \Swift_Message('Activation de votre compte Partagez-moi'))
                ->setFrom('noreply@partagezmoi.fr')
                ->setTo($utilisateur->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/activation.html.twig', ['token' => $utilisateur->getActivationToken()]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success' , 'Votre compte a été crée. Regardez vos mails pour l\'activer');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $utilisateur,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
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
