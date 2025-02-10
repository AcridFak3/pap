<?php

// src/Repository/TicketRepository.php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    // Método para buscar todos os tickets
    public function findAllTicketsQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.created_at', 'DESC');
    }

    // Método para buscar os tickets de um usuário específico
    public function findByUserTicketsQuery($user): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.userId = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('t.created_at', 'DESC');
    }
}
