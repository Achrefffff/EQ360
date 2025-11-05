<?php

namespace App\Repository;

use App\Entity\Objectif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objectif>
 */
class ObjectifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objectif::class);
    }

    /**
     * Trouve les objectifs par priorité
     */
    public function findByPriorite(string $priorite)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.priorite = :priorite')
            ->setParameter('priorite', $priorite)
            ->orderBy('o.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les objectifs par projet et statut
     */
    public function findByProjetAndStatut(int $projetId, string $statut)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.projet = :projetId')
            ->andWhere('o.statut = :statut')
            ->setParameter('projetId', $projetId)
            ->setParameter('statut', $statut)
            ->orderBy('o.priorite', 'DESC')
            ->getQuery()
            ->getResult();
    }
}