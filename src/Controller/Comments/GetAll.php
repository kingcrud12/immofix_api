<?php

namespace App\Controller\Comments;

use App\Controller\helpers\CommentReturnedAsArray;
use App\Entity\Comment;
use App\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Comments')]
#[Route('/api/tickets')]
final class GetAll extends AbstractController
{
    public function __construct(private readonly CommentServiceInterface $svc)
    {
    }
    #[Route('/{ticketId}/comments', name: 'api_ticket_comments', methods: ['GET'])]
    public function listForTicket(int $ticketId): JsonResponse
    {
        $list = $this->svc->getCommentsForTicket($ticketId);
        $commentReturned = new CommentReturnedAsArray();
        return $this->json(array_map(fn(Comment $c) => $commentReturned->toArray($c), $list));
    }
}
