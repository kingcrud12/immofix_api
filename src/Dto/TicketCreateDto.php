<?php
// src/Dto/Ticket/TicketCreateDto.php
namespace App\Dto;

use App\Interfaces\TicketCreateInput;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class TicketCreateDto implements TicketCreateInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 1000)]
    public string $description;


    #[Assert\Type('integer')]
    #[Assert\Positive]
    public ?int $assigneeId = null;

    #[Assert\Choice(callback: [self::class, 'statusValues'])]
    public ?string $status = null;

    #[Assert\Choice(callback: [self::class, 'priorityValues'])]
    public ?string $priority = null;

    // --- Implémentation de l'interface
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getAssigneeId(): ?int { return $this->assigneeId; }
    public function getStatus(): ?string { return $this->status; }
    public function getPriority(): ?string { return $this->priority; }

    public static function statusValues(): array
    {
        return array_map(static fn($c) => $c->value, TicketStatus::cases());
    }
    public static function priorityValues(): array
    {
        return array_map(static fn($c) => $c->value, TicketPriority::cases());
    }
}
