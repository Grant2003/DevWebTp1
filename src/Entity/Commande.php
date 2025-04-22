<?php

namespace App\Entity;

//-----------------------------------
//   Fichier : Categorie.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use App\Entity\CategorieRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\CategorieRepository::class)]


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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function idCategorie(): ?int
    {
        return $this->idCategorie;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
///
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getDateCommande(): ?string
    {
        return $this->nom;
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
