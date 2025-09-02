<?php

namespace App\Controller\Comments;
use App\Entity\User;
use App\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;


#[OA\Tag(name: 'Comments')]
#[Route('/api/tickets')]
final class Delete extends AbstractController
{
    public function __construct(private readonly CommentServiceInterface $svc)
    {
    }

    #[Route('/{ticketId}/comments/{id}', name: 'api_comment_delete', methods: ['DELETE'])]
    public function delete(
        int                 $id,
        #[CurrentUser] User $currentUser,
        int $ticketId
    ): JsonResponse
    {
        $this->svc->deleteComment($id,$ticketId, $currentUser);
        return $this->json(null, 204);
    }
}
