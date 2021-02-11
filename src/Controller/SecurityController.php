<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordType;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/password-oublie", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request,
                                      UtilisateurRepository $utilisateurRepository,
                                      \Swift_Mailer $mailer,
                                      TokenGeneratorInterface $tokenGenerator)
    {
        //on crée le formulaire
        $form = $this->createForm(ResetPasswordType::class);

        // on traite le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // on récupère les données
            $donnees = $form->getData();

            // On cherche si l'utilisateur a cet email
            $utilisateur= $utilisateurRepository->findOneBy($donnees['email']);

            // Si l'utilisateur n'existe pas
            if(!$utilisateur)
            {
                $this->addFlash('danger', 'Cet email n\'existe pas');
                $this->redirectToRoute('app_login');
            }

            $token = $tokenGenerator->generateToken();

            try {
                $utilisateur->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();

            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // On génère l'url de réinitialisation du password
            $url = $this->generateUrl('app_reset_password', ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL);

            // On envoie le message
            $message = (new \Swift_Message('Réinitialisation de votre password de votre compte Partagez-moi'))
                ->setFrom('noreply@partagezmoi.fr')
                ->setTo($utilisateur->getEmail())
                ->setBody(                   $this->renderView(
                    'email/reinitialized_password.html.twig', ['url' => $url]
                ),
                    'text/html');

            $mailer->send($message);

            // On crée le message flash
            $this->addFlash('success ', 'Un email de réinitialisation du password a été envoyé');

            return $this->redirectToRoute('app_login');
        }

        // On envoie vers la page de demande de l'email
        return $this->render('security/forgotten_password.html.twig', [
            'emailForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="app_reset_password")
     */
    public function resetPassword($token,
                                  UtilisateurRepository $utilisateurRepository,
                                  Request $request,
                                  UserPasswordEncoderInterface $userPasswordEncoder)
    {
        // On cherche l'utilisateur avec le token passé en url
        $utilisateur = $utilisateurRepository->findOneBy(['reset_token' => $token]);

        if(!$utilisateur) {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }

        if($request->isMethod("POST")){

            $utilisateur->setResetToken(null);

            // On chiffre le mot de passe
            $utilisateur->setPassword($userPasswordEncoder->encodePassword($utilisateur, $request->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'mot de passe changé avec succès');

            return $this->redirectToRoute('app_login');

        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }

    /**
     * @Route("/changer-password/{id}", name="password_change", methods={"GET", "POST"})
     */
    public function changePassword (Request $request,
                                    Utilisateur $utilisateur,
                                    UtilisateurRepository $utilisateurRepository,
                                    UserPasswordEncoderInterface $userPasswordEncoder)
    {
        //on crée le formulaire
        $form = $this->createForm(ChangePasswordFormType::class);

        // on traite le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $donnees = $form->getData();
            $utilisateur = $utilisateurRepository->find($utilisateur->getId());
            $encodedOldPasswordIsOk = $userPasswordEncoder->isPasswordValid($utilisateur, $donnees['oldPassword']);

            if($encodedOldPasswordIsOk) {

                $entityManager = $this->getDoctrine()->getManager();
                $utilisateur->setPassword($userPasswordEncoder->encodePassword($utilisateur, $donnees['newPassword']));
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                $this->addFlash('success ', 'Votre mot de passe a été modifié avec succès');
                return $this->redirectToRoute('utilisateur_edit', [
                    'id' => $utilisateur->getId()]);
            }

            else {
                $this->addFlash('danger ', 'Le mot de passe renseigné n\'est pas correct');
                return $this->redirectToRoute('utilisateur_edit', [
                    'id' => $utilisateur->getId()]);
            }
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
            'id' => $utilisateur->getId()
        ]);
    }
}
