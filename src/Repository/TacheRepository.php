<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    /**
     * Trouve les tâches par priorité et statut
     */
    public function findByPrioriteAndStatut(string $priorite, string $statut)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.priorite = :priorite')
            ->andWhere('t.statut = :statut')
            ->setParameter('priorite', $priorite)
            ->setParameter('statut', $statut)
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les tâches par objectif et difficulté
     */
    public function findByObjectifAndDifficulte(int $objectifId, int $difficulte)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.objectif = :objectifId')
            ->andWhere('t.difficulte = :difficulte')
            ->setParameter('objectifId', $objectifId)
            ->setParameter('difficulte', $difficulte)
            ->orderBy('t.priorite', 'DESC')
            ->getQuery()
            ->getResult();
    }
}