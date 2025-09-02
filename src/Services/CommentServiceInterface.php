<?php
// src/Services/CommentServiceInterface.php
namespace App\Services;

use App\Entity\Comment;
use App\Interfaces\CommentInput;
use Symfony\Component\Security\Core\User\UserInterface;

interface CommentServiceInterface
{
    public function createComment(CommentInput $input,int $id, UserInterface $currentUser): Comment;
    public function updateComment(int $id, CommentInput $input, UserInterface $currentUser): Comment;
    public function deleteComment(int $id, UserInterface $currentUser): void;

    public function getComment(int $id): Comment;

    /** @return array<Comment> */
    public function getCommentsForTicket(int $ticketId): array;
}
