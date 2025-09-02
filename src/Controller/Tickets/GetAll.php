<?php

namespace App\Controller\Tickets;

use App\Dto\TicketCreateDto;
use App\Dto\TicketUpdateDto;
use App\Entity\Ticket as TicketEntity;
use App\Entity\User;
use App\Services\TicketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/tickets')]
final class GetAll extends AbstractController
{
    public function __construct(private readonly TicketServiceInterface $svc) {}

    #[Route('/{id}', name: 'api_ticket_get', methods: ['GET'])]
    public function get(
        int $id,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $ticket = $this->svc->getTicket($id, $currentUser);

        return $this->json([
            'id'        => $ticket->getId(),
            'title'     => $ticket->getTitle(),
            'status'    => $ticket->getStatus()->value,
            'priority'  => $ticket->getPriority()->value,
            'author'    => $ticket->getAuthor()?->getEmail(),
            'assignee'  => $ticket->getAssignee()?->getEmail(),
        ]);
    }
}
