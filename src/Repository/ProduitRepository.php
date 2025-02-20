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
    public function findWithCriteria($categorie, $searchField) {
        $queryBuilder = $this->createQueryBuilder('c'); //SELECT * FROM champions c

        if($searchField != null) {
            $queryBuilder->andWhere('c.name LIKE :searchFilter') // WHERE c.name LIKE %{searchField}%
                ->orWhere('c.description LIKE :searchFilter') // OR c.description LIKE %{searchField}%
                ->setParameter('searchFilter', '%'.$searchField.'%'); 
        }

        if($categorie != null) {
            $queryBuilder->andWhere('c.mainRole = :mainRole')
                ->setParameter('mainRole', $categorie);
        }
        

        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return Produits[] Returns an array of Produits objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produits
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
