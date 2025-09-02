<?php
namespace App\Dto;

use App\Interfaces\CommentInput;
use Symfony\Component\Validator\Constraints as Assert;

class CommentDto implements CommentInput {
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 5000)]
    public string $text;


    #[Assert\Type('integer')]
    #[Assert\Positive]
    public int $ticketId;

    public function getText(): string { return $this->text; }
    public function getTicketId(): int { return $this->ticketId; }
}
