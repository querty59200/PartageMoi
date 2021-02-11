<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Repository\ProduitRepository;
use App\Repository\ReactionRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    private $produitRepository;
    private $utilisateurRepository;
    private $reactionRepository;


    public function __construct(ProduitRepository $produitRepository,
                                UtilisateurRepository $utilisateurRepository,
                                ReactionRepository $reactionRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->produitRepository = $produitRepository;
        $this->reactionRepository = $reactionRepository;


    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR'); // create a French faker

        $produits = $this->produitRepository->findAll();
        $utilisateurs = $this->utilisateurRepository->findAll();
        $reactions = $this->reactionRepository->findAll();

        for($i= 0; $i < 5; $i++){
            $location = new Location();
            $location->setMontantLocation(mt_rand(1,5));
            $location->setDateDebut(new \DateTime('2021-01-01'));
            $location->setDateFin($faker->dateTimeBetween('- 2weeks', 'now'));
            $location->setProduit($faker->randomElement($produits));
            $location->setUtilisateur($faker->randomElement($utilisateurs));
            $location->setReaction($faker->randomElement($reactions));

            $manager->persist($location);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            ReactionFixtures::class,
            UtilisateurFixtures::class,
            ProduitFixtures::class
        );
    }
}
