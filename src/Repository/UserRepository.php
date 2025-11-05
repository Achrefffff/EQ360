<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Exemple de méthode pour trouver un utilisateur par email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Exemple de méthode pour trouver tous les utilisateurs par prénom
     */
    public function findByPrenom(string $prenom)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.prenom LIKE :prenom')
            ->setParameter('prenom', '%'.$prenom.'%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}