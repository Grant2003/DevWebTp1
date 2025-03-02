<?php

namespace App\Repository;

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
    public function findWithCriteria($categorie, $searchField, $limit = 10, $offset = 0) {
        $queryBuilder = $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
    
        if ($searchField != null) {
            $queryBuilder->andWhere('c.nom LIKE :searchFilter')
                ->orWhere('c.description LIKE :searchFilter')
                ->setParameter('searchFilter', '%' . $searchField . '%'); 
        }
    
        if ($categorie != null) {
            $queryBuilder->andWhere('c.idCategorie = :idCategorie')
                ->setParameter('idCategorie', $categorie);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
}
