<?php

namespace App\Repository;
//-----------------------------------
//   Fichier : clientRepository.php
//   Par:      Anthony Grenier
//   Date :    2025-3-27
//-----------------------------------

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function findWithCriteria($id) {
        $queryBuilder = $this->createQueryBuilder('c');    
    
        if ($categorie != null) {
            $queryBuilder->andWhere('c.idCommande = :idCommande')
                ->setParameter('idCommande', $id);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }

}
