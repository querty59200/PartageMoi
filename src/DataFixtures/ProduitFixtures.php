<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\DepotRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    private $categorieRepository;
    private $depotRepository;

    public function __construct(DepotRepository $depotRepository,
                                CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
        $this->depotRepository = $depotRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $categories = $this->categorieRepository->findAll();
        $depots = $this->depotRepository->findAll();

        for($i = 0; $i < 20; $i++){
            $produit = new Produit();
            $produit->setNom($faker->text(10));
            $produit->setMarque($faker->randomElement(['Seb', 'Krupps', 'Moulinex']));
            $produit->setCategorie($faker->randomElement($categories));
            $produit->setContenu($faker->text(20));
            $produit->setCreatedAt(new \DateTime('now'));
            $produit->setDepot($faker->randomElement($depots));
            $manager->persist($produit);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategorieFixtures::class,
        );
    }
}
