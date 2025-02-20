<?php

namespace App\Repository;
               
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
        $queryBuilder = $this->createQueryBuilder('c'); //SELECT * FROM champions c

        if($searchField != null) {
            $queryBuilder->andWhere('c.name LIKE :searchFilter') // WHERE c.name LIKE %{searchField}%
                ->orWhere('c.description LIKE :searchFilter') // OR c.description LIKE %{searchField}%
                ->setParameter('searchFilter', '%'.$searchField.'%'); 
        }

        if($categorie != null) {
            $queryBuilder->andWhere('c.idCategorie = :idCategorie')
                ->setParameter('idCategorie', $categorie);
        }
        

        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return Categories[] Returns an array of Categories objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Categories
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
