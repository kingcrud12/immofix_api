<?php

namespace App\Controller\Tickets;
use App\Entity\User;
use App\Services\TicketServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tickets')]
#[Route('/api/tickets')]
final class Delete extends AbstractController
{
    public function __construct(private readonly TicketServiceInterface $svc)
    {
    }

    #[Route('/{id}', name: 'api_ticket_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $this->svc->deleteTicket($id, $currentUser);
        return $this->json(null, 204);
    }
}
