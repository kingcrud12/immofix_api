<?php

namespace App\Controller\Comments;

use App\Controller\helpers\CommentReturnedAsArray;
use App\Entity\Comment;
use App\Entity\User;
use App\Dto\CommentDto;
use App\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/tickets')]
final class Update extends AbstractController
{
    public function __construct(private readonly CommentServiceInterface $svc)
    {
    }

    #[Route('{ticketId}/comments/{id}', name: 'api_comment_update', methods: ['PATCH'])]
    public function update(
        int                             $id,
        #[MapRequestPayload] CommentDto $dto,
        #[CurrentUser] User             $currentUser,
        int $ticketId
    ): JsonResponse
    {
        $c = $this->svc->updateComment($id, $ticketId, $dto,  $currentUser);
        $commentReturned = new CommentReturnedAsArray();
        return $this->json($commentReturned->toArray($c));
    }

}
