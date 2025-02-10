<?php
namespace App\Security;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TicketVoter extends Voter
{
    public const VIEW = 'view_ticket';
    public const EDIT = 'edit_ticket';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT]) && $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false; // Bloqueia se o utilizador não estiver autenticado
        }

        /** @var Ticket $ticket */
        $ticket = $subject;

        // O dono do ticket pode ver e editar
        if ($ticket->getUserId() === $user) {
            return true;
        }

        // (Opcional) Se for um admin, pode aceder a todos os tickets
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_STAFF')) {
            return true;
        }

        return false; // Se não for dono nem admin, nega o acesso
    }
}
