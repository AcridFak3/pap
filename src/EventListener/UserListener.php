<?php

// src/EventListener/TicketListener.php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserListener
{
    // Este método será chamado antes da persistência da entidade
    /**
     * @throws \DateMalformedStringException
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        // Obtendo a entidade a partir dos argumentos do evento
        $user = $args->getObject();

        // No método prePersist e preFlush

        // Verifica se a entidade é do tipo Ticket
        if ($user instanceof User) {
            if (!$user->getCreatedAt()) {
                $user->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            }

            if (!$user->getUpdatedAt()) {
                $user->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
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
        $user = $args->getObject();

        // Verifica se a entidade é do tipo Ticket
        if ($user instanceof User) {
            // Atualiza a data de updatedAt antes de persistir as mudanças
            $user->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        }
    }
}
