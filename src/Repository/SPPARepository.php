<?php

namespace App\Repository;

use App\Entity\SPPA;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SPPA>
 */
class SPPARepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SPPA::class);
    }

    /**
     * Trouve tous les SPPA d'un utilisateur
     */
    public function findByUser(int $userId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('s.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les SPPA par niveau minimum
     */
    public function findByNiveauMinimum(float $niveau)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.niveau >= :niveau')
            ->setParameter('niveau', $niveau)
            ->orderBy('s.niveau', 'DESC')
            ->getQuery()
            ->getResult();
    }
}