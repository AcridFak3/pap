<?php

// src/EventListener/TicketListener.php

namespace App\EventListener;

use App\Entity\TicketLog;
use Doctrine\ORM\Event\PrePersistEventArgs;

class TicketLogListener
{
    // Este método será chamado antes da persistência da entidade
    /**
     * @throws \DateMalformedStringException
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        // Obtendo a entidade a partir dos argumentos do evento
        $ticketLog = $args->getObject();

        // No método prePersist e preFlush

        // Verifica se a entidade é do tipo Ticket
        if ($ticketLog instanceof TicketLog) {
            // Atribuindo valores default para a entidade
            if (!$ticketLog->getCreatedAt()) {
                $ticketLog->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            }
        }
    }
}
