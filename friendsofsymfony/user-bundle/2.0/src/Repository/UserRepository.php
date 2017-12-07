<?php

namespace App\Repository;

use App\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * User Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Entity\User::class);
    }
}
