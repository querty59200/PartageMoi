<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Form\ReactionType;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReactionController extends AbstractController
{
    /**
     * @Route("/cloturer-location/{id}", name="location_cloture")
     */
    public function register($id,
                             Request $request,
                             LocationRepository $locationRepository,
                             \Swift_Mailer $mailer) : Response
    {
        $location = $locationRepository->find($id);
        $location->getReaction()->setReactionToken(md5(uniqid()));

        $message = (new \Swift_Message('Retour d\'expérience sur votre dernière location sur Partagez-moi'))
            ->setFrom('noreply@partagezmoi.fr')
            ->setTo($location->getUtilisateur()->getEmail())
            ->setBody(
                $this->renderView(
                    'email/reaction_sur_la_location.html.twig', ['token' => $location->getReaction()->getReactionToken()]
                ),
                'text/html'
            );

            $mailer->send($message);

        return $this->redirectToRoute('locations');
    }
}
