<?php

namespace App\DataFixtures;

use App\Entity\Pret;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PretFixtures extends Fixture implements DependentFixtureInterface
{
    private $produitRepository;
    private $utilisateurRepository;

    public function __construct(ProduitRepository $produitRepository,
                                UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->produitRepository = $produitRepository;

    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR'); // create a French faker

        $produits = $this->produitRepository->findAll();
        $utilisateurs = $this->utilisateurRepository->findAll();

        for($i= 0; $i < 5; $i++){
            $pret = new Pret();
            $pret->setMontantRente(mt_rand(1,5));
            $pret->setDateDebut(new \DateTime('2021-01-28'));
            $pret->setDateFin($faker->dateTimeBetween('- 2weeks', 'now'));
            $pret->setProduit($faker->randomElement($produits));
            $pret->setUtilisateur($faker->randomElement($utilisateurs));

            $manager->persist($pret);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UtilisateurFixtures::class,
            ProduitFixtures::class,
        );
    }
}
