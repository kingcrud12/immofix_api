<?php

// src/Controller/CommentController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Dto\CommentDto;

// ton DTO qui implémente CommentInput
use App\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/tickets')]
final class CommentController extends AbstractController
{
    public function __construct(private readonly CommentServiceInterface $svc)
    {
    }

    #[Route('/{ticketId}/comments', name: 'api_comment_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CommentDto $dto,
        #[CurrentUser] User             $currentUser,
        int $ticketId,
    ): JsonResponse
    {
        $c = $this->svc->createComment($dto,$ticketId, $currentUser);
        return $this->json($this->toArray($c), 201);
    }

    // Lecture publique d’un commentaire
    #[Route('/comments/{id}', name: 'api_comment_get', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        $c = $this->svc->getComment($id);
        return $this->json($this->toArray($c));
    }

    // Lecture publique: tous les commentaires d’un ticket
    #[Route('/tickets/{ticketId}/comments', name: 'api_ticket_comments', methods: ['GET'])]
    public function listForTicket(int $ticketId): JsonResponse
    {
        $list = $this->svc->getCommentsForTicket($ticketId);
        return $this->json(array_map(fn(Comment $c) => $this->toArray($c), $list));
    }

    #[Route('/comments/{id}', name: 'api_comment_update', methods: ['PATCH'])]
    public function update(
        int                             $id,
        #[MapRequestPayload] CommentDto $dto,
        #[CurrentUser] User             $currentUser
    ): JsonResponse
    {
        // On ne change que ce qui est présent dans le payload :
        // si tu veux gérer "clé absente vs null explicite", ajoute un mécanisme comme pour les tickets.
        $c = $this->svc->updateComment($id, $dto, $currentUser);
        return $this->json($this->toArray($c));
    }

    #[Route('/comments/{id}', name: 'api_comment_delete', methods: ['DELETE'])]
    public function delete(
        int                 $id,
        #[CurrentUser] User $currentUser
    ): JsonResponse
    {
        $this->svc->deleteComment($id, $currentUser);
        return $this->json(null, 204);
    }

    /** Normalisation simple pour la réponse JSON */
    private function toArray(Comment $c): array
    {
        return [
            'id' => $c->getId(),
            'text' => $c->getText(),
            'author' => $c->getAuthor()?->getEmail(),
            'ticket' => $c->getTicketId()?->getId(),
            // ajoute createdAt/updatedAt si dispo, ex.:
            // 'createdAt' => $c->getCreatedAt()?->format(DATE_ATOM),
        ];
    }
}
