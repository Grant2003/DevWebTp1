<?php

namespace App\Entity;

//-----------------------------------
//   Fichier : Categorie.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use App\Entity\CommandeRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\CommandeRepository::class)]


#[ORM\Table(name:'commande')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idCommande')]
    private ?int $idCommande = null;
    
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $dateCommande = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'client_utilisateur', referencedColumnName: 'utilisateur', nullable: false)]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandeDetail::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $commandeDetails;

    public function __construct()
    {
        $this->commandeDetails = new ArrayCollection();
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getDateCommande(): ?string
    {
        return $this->dateCommande;
    }
    public function getCommandeDetails(): ?Collection
    {
        return $this->commandeDetails;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    public function setClient(Client $client): static
    {
        $this->client = $client;

        return $this;
    }
    public function setDateCommande(DateTime $datecomm): static
    {
        $this->dateCommande = $datecomm;

        return $this;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
