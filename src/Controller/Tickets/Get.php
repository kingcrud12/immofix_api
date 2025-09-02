<?php

namespace App\Controller\Tickets;
use App\Entity\Ticket as TicketEntity;
use App\Entity\User;
use App\Services\TicketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/tickets')]
final class Get extends AbstractController
{
    public function __construct(private readonly TicketServiceInterface $svc) {}

    #[Route('', name: 'api_ticket_list', methods: ['GET'])]
    public function list(#[CurrentUser] User $currentUser): JsonResponse
    {
        $tickets = $this->svc->getTickets($currentUser);

        return $this->json(array_map(fn(TicketEntity $t) => [
            'id'        => $t->getId(),
            'title'     => $t->getTitle(),
            'status'    => $t->getStatus()->value,
            'priority'  => $t->getPriority()->value,
            'author'    => $t->getAuthor()?->getFirstname(),
            'assignee'  => $t->getAssignee()?->getEmail(),
        ], $tickets));
    }
}
