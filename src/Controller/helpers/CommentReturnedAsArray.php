<?php

namespace App\Controller\helpers;

use App\Entity\Comment;

class CommentReturnedAsArray
{
    public function toArray(Comment $c): array
    {
        return [
            'id' => $c->getId(),
            'text' => $c->getText(),
            'author' => $c->getAuthor()?->getFirstname(),
            'ticket' => $c->getTicketId()?->getId(),
        ];
    }

}
