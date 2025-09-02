<?php

namespace App\Controller\Tickets;
use App\Dto\TicketUpdateDto;
use App\Entity\User;
use App\Services\TicketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tickets')]
#[Route('/api/tickets')]
class Update extends AbstractController {
    public function __construct(private readonly TicketServiceInterface $svc) {}

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


}
