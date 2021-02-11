<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location", uniqueConstraints={
@ORM\UniqueConstraint(name="Une_location_par_produit_par_date",
columns={"utilisateur_id", "produit_id", "date_debut"}) } )
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $montantLocation;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=Locataire::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $locataire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantLocation(): ?float
    {
        return $this->montantLocation;
    }

    public function setMontantLocation(float $montantLocation): self
    {
        $this->montantLocation = $montantLocation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->dateFin = $date_fin;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?Utilisateur $Utilisateur): self
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $Produit): self
    {
        $this->produit = $Produit;

        return $this;
    }

    public function getLocataire(): ?Locataire
    {
        return $this->locataire;
    }

    public function setLocataire(?Locataire $locataire): self
    {
        $this->locataire = $locataire;

        return $this;
    }
}
