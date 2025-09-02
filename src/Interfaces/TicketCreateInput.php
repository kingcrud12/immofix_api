<?php
namespace App\Interfaces;

interface TicketCreateInput {
    public function getTitle(): string;
    public function getDescription(): string;
    public function getStatus(): ?string;
    public function getPriority(): ?string;
}
