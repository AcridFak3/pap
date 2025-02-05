<?php

// src/EventListener/TicketListener.php

namespace App\EventListener;

use App\Entity\Ticket;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TicketListener
{
    // Este método será chamado antes da persistência da entidade
    /**
     * @throws \DateMalformedStringException
     */
    public function prePersist(PrePersistEventArgs $args): void
    {

        // Obtendo a entidade a partir dos argumentos do evento
        $ticket = $args->getObject();

        // No método prePersist e preFlush




        // Verifica se a entidade é do tipo Ticket
        if ($ticket instanceof Ticket) {
            // Atribuindo valores default para a entidade
            if (!$ticket->getStatus()) {
                $ticket->setStatus('L'); // Definindo 'L' como status padrão
            }

            if (!$ticket->getCreatedAt()) {
                $ticket->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            }

            if (!$ticket->getUpdatedAt()) {
                $ticket->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            }
        }
    }

    // Este método será chamado antes do flush, ou seja, antes de persistir as alterações

    /**
     * @throws \DateMalformedStringException
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        // Obtendo a entidade a partir dos argumentos do evento
        $ticket = $args->getObject();

        // Verifica se a entidade é do tipo Ticket
        if ($ticket instanceof Ticket) {
            // Atualiza a data de updatedAt antes de persistir as mudanças
            $ticket->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        }
    }
}
