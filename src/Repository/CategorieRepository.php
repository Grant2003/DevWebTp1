<?php

namespace App\Repository;
//-----------------------------------
//   Fichier : CategorieRepository.php
//   Par:      Anthony Grenier
//   Date :    2025-2-22
//-----------------------------------
               
use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }
    public function findWithCriteria($categorie, $searchField) {
        $queryBuilder = $this->createQueryBuilder('c'); 

        if($searchField != null) {
            $queryBuilder->andWhere('c.name LIKE :searchFilter') 
                ->orWhere('c.description LIKE :searchFilter') 
                ->setParameter('searchFilter', '%'.$searchField.'%'); 
        }

        if($categorie != null) {
            $queryBuilder->andWhere('c.idCategorie = :idCategorie')
                ->setParameter('idCategorie', $categorie);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
}
