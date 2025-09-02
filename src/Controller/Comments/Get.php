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
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Comments')]
#[Route('/api/tickets')]
final class Get extends AbstractController
{
    public function __construct(private readonly CommentServiceInterface $svc)
    {
    }

    #[Route('/{ticketId}/comments/{id}', name: 'api_comment_get', methods: ['GET'])]
    public function getOne(int $id, int $ticketId): JsonResponse
    {
        $c = $this->svc->getComment($id, $ticketId);

        $commentReturned = new CommentReturnedAsArray();

        return $this->json($commentReturned->toArray($c));
    }

}
