<?php

namespace App\Controller\Comments;

use App\Controller\helpers\CommentReturnedAsArray;
use App\Entity\User;
use App\Dto\CommentDto;
use App\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Comments')]
#[Route('/api/tickets')]
final class Create extends AbstractController
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
        $commentReturned = new CommentReturnedAsArray();
        return $this->json($commentReturned->toArray($c), 201);
    }
}
