<?php

namespace App\Repository;
//-----------------------------------
//   Fichier : CommandeRepository.php
//   Par:      Anthony Grenier
//   Date :    2025-4-18
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
    
        if ($id != null) {
            $queryBuilder->andWhere('c.idCommande = :idCommande')
                ->setParameter('idCommande', $id);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
    public function findByClientOrdered($client)
    {
        return $this->findBy(
            ['client' => $client],
            ['dateCommande' => 'DESC'] 
        );
    }


}
