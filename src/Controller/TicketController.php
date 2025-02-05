<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\ticket\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(name: 'app_ticket_all', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $user = $this->security->getUser();
        $roles = $user->getRoles();

        // Verifica se o usuário tem o papel de 'ROLE_ADMIN'
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->render('ticket/all.html.twig', [
                'tickets' => $ticketRepository->findAll(),
            ]);
        } else {
            return $this->render('ticket/all.html.twig', [
                'tickets' => $ticketRepository->findById($user->getId()),  // Passando o ID do usuário
            ]);
        }
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $ticket = new Ticket();
        $user = $this->security->getUser();
        $ticket->setUserId($user);
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        $ticket
            ->setStatus('p')
            ->setPriority('l')
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setCreatedAt(new \DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_all', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/create.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        // Verifica se o usuário tem a role ROLE_ADMIN
        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_all', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
            'isAdmin' => $isAdmin,  // Passando a variável isAdmin para o Twig
        ]);
    }


    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_all', [], Response::HTTP_SEE_OTHER);
    }
}
