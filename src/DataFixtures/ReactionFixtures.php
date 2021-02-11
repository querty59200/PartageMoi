<?php

namespace App\DataFixtures;

use App\Entity\Reaction;
use App\Repository\LocationRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i= 0; $i < 5; $i++){
            $reaction = new Reaction();
            $reaction->setType(mt_rand(0, 1));
            $reaction->setVotedAt($faker->dateTimeBetween('- 2years', 'now'));
            $manager->persist($reaction);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            UtilisateurFixtures::class,
        );
    }
}
