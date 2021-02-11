<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurFixtures extends Fixture
{
    private $encoder;
    private $roles;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->roles = array('ROLE_ADMI','ROLE_LOCATAIRE', 'ROLE_PROPRIETAIRE', 'ROLE_GERANT');
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR'); // create a French faker

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('admi@admi.fr');
        $utilisateur->setRoles((array)$this->roles[0]);
        $utilisateur->setPassword($this->encodePassword($utilisateur, 'azerty'));
        $utilisateur->setPrenom($faker->firstName());
        $utilisateur->setNom($faker->name());
        $utilisateur->setTelephone('06 01 02 03 04');
        $manager->persist($utilisateur);
        $manager->flush();

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('locataire@locataire.fr');
        $utilisateur->setRoles((array)$this->roles[1]);
        $utilisateur->setPassword($this->encodePassword($utilisateur, 'azerty'));
        $utilisateur->setPrenom($faker->firstName());
        $utilisateur->setNom($faker->name());
        $utilisateur->setTelephone('06 01 02 03 04');
        $manager->persist($utilisateur);

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('proprietaire@proprietaire.fr');
        $utilisateur->setRoles((array)$this->roles[2]);
        $utilisateur->setPassword($this->encodePassword($utilisateur, 'azerty'));
        $utilisateur->setPrenom($faker->firstName());
        $utilisateur->setNom($faker->name());
        $utilisateur->setTelephone('06 01 02 03 04');
        $manager->persist($utilisateur);

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('gerant@gerant.fr');
        $utilisateur->setRoles((array)$this->roles[3]);
        $utilisateur->setPassword($this->encodePassword($utilisateur, 'azerty'));
        $utilisateur->setPrenom($faker->firstName());
        $utilisateur->setNom($faker->name());
        $utilisateur->setTelephone('06 01 02 03 04');
        $manager->persist($utilisateur);

        $manager->flush();

        for($i = 0; $i < 50; $i++){
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($faker->email());
            $utilisateur->setRoles((array)$this->roles[2]);
            $utilisateur->setPassword($this->encodePassword($utilisateur, 'azerty'));
            $utilisateur->setPrenom($faker->firstName());
            $utilisateur->setNom($faker->name());
            $utilisateur->setTelephone('06 01 02 03 04');
            $manager->persist($utilisateur);
        }
            $manager->flush();

    }

    private function encodePassword(Utilisateur $utilisateur, string $plainPassword) : string{
        return  $this->encoder->encodePassword($utilisateur, $plainPassword);
    }
}
