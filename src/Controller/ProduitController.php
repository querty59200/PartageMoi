<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\ReactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("gerant/produits", name="produitsViewGerant", methods={"GET"})
     */
    public function produitsViewGerant(ProduitRepository $produitRepository): Response
    {
        return $this->render('gerant/produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/produits", name="produits", methods={"GET"})
     */
    public function produits(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/produit/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $produit->setCreatedAt(new \DateTime('now'));

            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichierName = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichierName);

                $imageTemp = new Image();
                $imageTemp->setNom($fichierName);
                $produit->addImage($imageTemp);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit crée le ' . $produit->getCreatedAt());

            return $this->redirectToRoute('produits');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichierName = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichierName);

                $imageTemp = new Image();
                $imageTemp->setNom($fichierName);
                $produit->addImage($imageTemp);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit bien édité');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé');
        }

        return $this->redirectToRoute('produits');
    }

    /**
     * @Route("/{id}/reaction", name="produit_reaction", methods={"GET"})
     */
    public function like(Request $request,
                         Produit $produit,
                         EntityManagerInterface $entityManager,
                         ReactionRepository $reactionRepository) : Response
    {
        $user = $this->getUser();

        if(!$user){
            return $this->json([
                'code' => '403'
            ],403);
        }

        if($produit->isLikedByUser($user)){
            $reaction = $reactionRepository->findOneBy([
                'user' => $user,
                'produit' => $produit
            ]);

            $entityManager->remove($reaction);
            $entityManager->flush();

            return $this->json([
                'reactions' => $reactionRepository->count([
                    'produit' => $produit])
            ], 200);

        } else {

            $reaction = new Reaction();
            $reaction->setUser($user);
            $reaction->setLuggage($produit);

            $entityManager->persist($reaction);
            $entityManager->flush();

            return $this->json([
                'reactions' => $reactionRepository->count([
                    'produit' => $produit])
            ],200);
        }
    }
}
