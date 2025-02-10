<?php

// src/EventListener/TicketListener.php

namespace App\EventListener;

use App\Entity\Comment;
use Doctrine\ORM\Event\PrePersistEventArgs;

class CommentListener
{
    // Este método será chamado antes da persistência da entidade
    /**
     * @throws \DateMalformedStringException
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        // Obtendo a entidade a partir dos argumentos do evento
        $comment = $args->getObject();

        // No método prePersist e preFlush

        // Verifica se a entidade é do tipo Ticket
        if ($comment instanceof Comment) {
            if (!$comment->getCreatedAt()) {
                $comment->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            }
        }
    }
}
