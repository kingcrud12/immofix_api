<?php
// src/Controller/TicketController.php
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
final class TicketController extends AbstractController
{
    public function __construct(private readonly TicketServiceInterface $svc) {}

    #[Route('', name: 'api_ticket_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] TicketCreateDto $dto,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $ticket = $this->svc->createTicket($dto, $currentUser);

        return $this->json([
            'id'        => $ticket->getId(),
            'title'     => $ticket->getTitle(),
            'status'    => $ticket->getStatus()->value,
            'priority'  => $ticket->getPriority()->value,
            'author'    => $ticket->getAuthor()?->getFirstname(),
            'assignee'  => $ticket->getAssignee()?->getId(),
        ], 201);
    }

    #[Route('/{id}', name: 'api_ticket_update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        #[MapRequestPayload] TicketUpdateDto $dto,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        if (method_exists($dto, 'setKeys')) {
            $payload = json_decode($request->getContent(), true) ?? [];
            $dto->setKeys(array_keys($payload));
        }

        $ticket = $this->svc->updateTicket($id, $dto, $currentUser);

        return $this->json([
            'id'        => $ticket->getId(),
            'title'     => $ticket->getTitle(),
            'status'    => $ticket->getStatus()->value,
            'priority'  => $ticket->getPriority()->value,
            'author'    => $ticket->getAuthor()?->getId(),
            'assignee'  => $ticket->getAssignee()?->getId(),
        ]);
    }



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
