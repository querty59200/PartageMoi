<?php

namespace App\DataFixtures;

use App\Entity\DonneeEconomique;
use App\Repository\ProduitRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DonneeEconomiqueFixtures extends Fixture implements DependentFixtureInterface
{
    private $produitRepository;

    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $produits = $this->produitRepository->findAll();

        foreach ($produits as $produit) {
            for($i = 1; $i <= 30; $i++){
                if(mt_rand(0, 100) < 50){
                    $donneeEconomique = new DonneeEconomique();
                    $donneeEconomique->setProduit($produit);
                    $donneeEconomique->setDate(new \DateTime('2021-01-' . $i));
                    $donneeEconomique->setPrixLocation(mt_rand(2, 5));
                    $donneeEconomique->setPrixRente(mt_rand(0.1, 1.9));
                    $manager->persist($donneeEconomique);
                }
        }
        $manager->flush();
        }
    }

        public function getDependencies()
    {
        return array(
            ProduitFixtures::class,
        );
    }
}
