<?php

// src/Service/TicketServiceInterface.php
namespace App\Services;

use App\Interfaces\TicketCreateInput;
use App\Interfaces\TicketUpdateInput;
use App\Entity\Ticket;
use Symfony\Component\Security\Core\User\UserInterface;

interface TicketServiceInterface
{
    public function createTicket(TicketCreateInput $input, UserInterface $currentUser): Ticket;

    public function updateTicket(int $id, TicketUpdateInput $input, UserInterface $currentUser): Ticket;

    public function deleteTicket(int $id, UserInterface $currentUser): void;

    public function getTicket(int $id, UserInterface $currentUser): Ticket;

    public function getTickets(UserInterface $currentUser): array;
}
