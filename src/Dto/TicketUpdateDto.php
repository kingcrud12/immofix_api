<?php
// src/Dto/Ticket/TicketUpdateDto.php
namespace App\Dto;

use App\Interfaces\TicketUpdateInput;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class TicketUpdateDto implements TicketUpdateInput
{
    // Tout est optionnel en update (PATCH)
    #[Assert\Length(min: 3, max: 255)]
    public ?string $title = null;

    #[Assert\Length(min: 3, max: 1000)]
    public ?string $description = null;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    public ?int $assigneeId = null;

    #[Assert\Choice(callback: [self::class, 'statusValues'])]
    public ?string $status = null;

    #[Assert\Choice(callback: [self::class, 'priorityValues'])]
    public ?string $priority = null;

    // --- Implémentation de l'interface
    public function getTitle(): ?string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getAssigneeId(): ?int { return $this->assigneeId; }
    public function getStatus(): ?string { return $this->status; }
    public function getPriority(): ?string { return $this->priority; }

    // --- Helpers pour la validation
    public static function statusValues(): array
    {
        return array_map(static fn($c) => $c->value, TicketStatus::cases());
    }
    public static function priorityValues(): array
    {
        return array_map(static fn($c) => $c->value, TicketPriority::cases());
    }
}
