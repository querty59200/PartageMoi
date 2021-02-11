<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
            $categorie = new Categorie();
            $categorie->setNom('Crepière');
            $manager->persist($categorie);

            $categorie = new Categorie();
            $categorie->setNom('Appareil à raclette');
            $manager->persist($categorie);

            $categorie = new Categorie();
            $categorie->setNom( 'Plancha');
            $manager->persist($categorie);

            $manager->flush();
    }
}
