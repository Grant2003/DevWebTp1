<?php

namespace App\Repository;
//-----------------------------------
//   Fichier : ProduitRepository.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }
    public function findWithCriteria($searchField) {
        $queryBuilder = $this->createQueryBuilder('c');    
        if ($searchField != null) {
            $queryBuilder->andWhere('c.utilisateur LIKE :searchFilter')
                ->orWhere('c.nom LIKE :searchFilter')
                ->setParameter('searchFilter', '%' . $searchField . '%'); 
        }
    
    
        return $queryBuilder->getQuery()->getResult();
    }
}
