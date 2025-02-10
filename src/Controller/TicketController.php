<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\comment\CommentType;
use App\Form\ticket\TicketType;
use App\Form\ticket\TicketTypeCreate;
use App\Form\TicketLogType;
use App\Repository\TicketLogRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/ticket')]
final class TicketController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(name: 'app_ticket_all', methods: ['GET'])]
    public function index(Request $request, TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $isAdmin = in_array('ROLE_ADMIN' || 'ROLE_STAFF', $user->getRoles());
        $isStaff = in_array('ROLE_STAFF', $user->getRoles());

        // Definir a página atual e o limite de tickets por página
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        // Buscar os tickets usando QueryBuilder
        if ($isAdmin) {
            $queryBuilder = $ticketRepository->findAllTicketsQuery();
        } else {
            $queryBuilder = $ticketRepository->findByUserTicketsQuery($user);
        }

        // Executar a consulta paginada
        $tickets = $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Contar o total de tickets
        $totalTickets = (int) $ticketRepository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Calcular o total de páginas
        $totalPages = max(1, ceil($totalTickets / $limit));

        return $this->render('ticket/all.html.twig', [
            'tickets' => $tickets,
            'isAdmin' => $isAdmin,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $ticket = new Ticket();
        $user = $this->security->getUser();
        $ticket->setUserId($user);
        $form = $this->createForm(TicketTypeCreate::class, $ticket);
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

    #[Route('/{id}', name: 'app_ticket_show')]
    public function show(Ticket $ticket, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setTicketId($ticket);  // Use setTicket() instead of setTicketId
        $user = $this->security->getUser();
        $this->denyAccessUnlessGranted('view_ticket', $ticket);

        $comment->setUserId($user);  // Use setUser() to associate the comment with the logged-in user

        // Create the form for the comment
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Ticket e comentário atualizados com sucesso.');

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $isStaff = in_array('ROLE_STAFF', $this->getUser()->getRoles());

        return $this->render('ticket/show.html.twig', [
            'form' => $form,
            'ticket' => $ticket,
            'isAdmin' => $isAdmin,
            'isStaff' => $isStaff,
        ]);
    }

    #[Route('/{id}/edit')]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_all');
        }

        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $isStaff = in_array('ROLE_STAFF', $this->getUser()->getRoles());
        if ($isStaff) {
            //so para nao estar a emplementar mais isStaff checks no twig
            $isAdmin = true;
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/{id}/fix')]
    public function fix(Ticket $ticket, Request $request, TicketLogRepository $ticketLogRepository, EntityManagerInterface $entityManager): Response
    {
        // Buscar os logs do ticket, ordenados por data (timestamp)
        $logs = $ticketLogRepository->findBy(
            ['ticket' => $ticket],   // Filtro para buscar logs do ticket específico
            ['created_at' => 'DESC']   // Substitua "createdAt" pelo nome correto do campo
        );

        // Criar o formulário e manipular o POST
        $form = $this->createForm(TicketLogType::class);
        $form->handleRequest($request);

        // Verificar se o formulário foi enviado e validado
        if ($form->isSubmitted() && $form->isValid()) {
            // Adicionar o novo log
            $log = $form->getData();
            $log->setTicket($ticket);
            $log->setUser($this->getUser());  // Atribuir o usuário atual como autor do log

            $entityManager->persist($log);
            $entityManager->flush();

            // Mensagem de sucesso
            $this->addFlash('success', 'Log adicionado com sucesso!');

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        // Renderizar a página e passar os logs para o template
        return $this->render('ticket/fix.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'logs' => $logs,  // Passando os logs para o template
        ]);
    }

    #[Route('/ticket/{id}/delete', name: 'app_ticket_delete', methods: ['POST', 'DELETE'])]
    public function delete(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        // Valida o CSRF token
        if (!$this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token inválido.');

            return $this->redirectToRoute('app_ticket_all');
        }

        // Remove o ticket do banco de dados
        $em->remove($ticket);
        $em->flush();

        $this->addFlash('success', 'Ticket excluído com sucesso.');

        return $this->redirectToRoute('app_ticket_all');
    }
}
