<?php

namespace App\Repository;
//-----------------------------------
//   Fichier : ProduitRepository.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    public function findWithCriteria($categorie, $searchField) {
        $queryBuilder = $this->createQueryBuilder('p');    
        if ($searchField != null) {
            $queryBuilder->andWhere('p.nom LIKE :searchFilter')
                ->orWhere('p.description LIKE :searchFilter')
                ->setParameter('searchFilter', '%' . $searchField . '%'); 
        }
    
        if ($categorie != null) {
            $queryBuilder->andWhere('p.idCategorie = :idCategorie')
                ->setParameter('idCategorie', $categorie);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
}
