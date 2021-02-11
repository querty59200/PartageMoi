<?php

namespace App\Entity;

use App\Repository\ReactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReactionRepository::class)
 */
class Reaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $luggage;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUser(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
