<?php
namespace App\Interfaces;

interface TicketUpdateInput
{
    public function getTitle(): ?string;
    public function getDescription(): ?string;
    public function getAssigneeId(): ?int;
    public function getStatus(): ?string;
    public function getPriority(): ?string;
}
