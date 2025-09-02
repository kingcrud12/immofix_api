<?php

// src/Service/TicketService.php
namespace App\Services;

use App\Interfaces\TicketCreateInput;
use App\Interfaces\TicketUpdateInput;
use App\Entity\Ticket;
use App\Entity\User;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TicketService implements TicketServiceInterface
{
    public function __construct(
        private EntityManagerInterface        $em,
        private TicketRepository              $tickets,
        private UserRepository                $users,
        private AuthorizationCheckerInterface $auth
    )
    {
    }

    public function createTicket(TicketCreateInput $input, UserInterface $currentUser): Ticket
    {
        /** @var User $author */
        $author = $this->mustBeConcreteUser($currentUser);
        $assignee = $this->resolveAssigneeId($input->getAssigneeId());

        $ticket = new Ticket();
        $ticket->setTitle($input->getTitle());
        $ticket->setDescription($input->getDescription());
        $ticket->setAuthor($author);
        $ticket->setAssignee($assignee);

        // Enums (valeurs par défaut si null)
        $status = $input->getStatus() ?? TicketStatus::open->value;
        $priority = $input->getPriority() ?? TicketPriority::low->value;
        $ticket->setStatus(TicketStatus::from($status));
        $ticket->setPriority(TicketPriority::from($priority));

        $this->em->persist($ticket);
        $this->em->flush();

        return $ticket;
    }

    public function updateTicket(int $id, TicketUpdateInput $input, UserInterface $currentUser): Ticket
    {
        list($ticket, $isAdmin, $isAuthor, $isAssignee) = $this->Verify($id, $currentUser);


        $assigneeRestricted = $isAssignee && !$isAdmin && !$isAuthor;

        if (($t = $input->getTitle()) !== null) {
            if ($assigneeRestricted) throw new \RuntimeException('Seul le statut est modifiable par l’assigné');
            $ticket->setTitle($t);
        }
        if (($d = $input->getDescription()) !== null) {
            if ($assigneeRestricted) throw new \RuntimeException('Seul le statut est modifiable par l’assigné');
            $ticket->setDescription($d);
        }
        if (($p = $input->getPriority()) !== null) {
            if ($assigneeRestricted) throw new \RuntimeException('Seul le statut est modifiable par l’assigné');
            $ticket->setPriority(\App\Enum\TicketPriority::from($p));
        }
        $assigneeId = $input->getAssigneeId();
        if ($assigneeId !== null || $this->explicitNull($input, 'assigneeId')) {
            if ($assigneeRestricted) throw new \RuntimeException('Réassignation interdite pour l’assigné');
            $ticket->setAssignee($this->resolveAssigneeId($assigneeId));
        }

        if (($s = $input->getStatus()) !== null) {
            $ticket->setStatus(\App\Enum\TicketStatus::from($s));
        }

        $this->em->flush();
        return $ticket;
    }


    public function deleteTicket(int $id, UserInterface $currentUser): void
    {
        $ticket = $this->tickets->find($id) ?? throw new \RuntimeException('Ticket introuvable');

        if (
            !$this->auth->isGranted('ROLE_ADMIN') &&
            $ticket->getAuthor()?->getUserIdentifier() !== $currentUser->getUserIdentifier()
        ) {
            throw new \RuntimeException('Accès refusé');
        }

        $this->em->remove($ticket);
        $this->em->flush();
    }

    public function getTicket(int $id, UserInterface $currentUser): Ticket
    {
        list($ticket, $isAdmin, $isAuthor, $isAssignee) = $this->Verify($id, $currentUser);

        return $ticket;
    }


    public function getTickets(UserInterface $currentUser): array
    {
        if ($this->auth->isGranted('ROLE_ADMIN')) {
            // tri pour une liste stable
            return $this->tickets->findBy([], ['id' => 'DESC']);
        }

        /** @var User $user */
        $user = $this->mustBeConcreteUser($currentUser);

        // Filtrer en BDD plutôt que post-traiter en PHP
        return $this->tickets->createQueryBuilder('t')
            ->andWhere('t.author = :u OR t.assignee = :u')
            ->setParameter('u', $user)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    private function resolveAssigneeId(?int $assigneeId): ?User
    {
        if ($assigneeId === null) {
            return null;
        }
        $assignee = $this->users->find($assigneeId);
        if (!$assignee) {
            throw new \RuntimeException('assigneeId inconnu');
        }
        return $assignee;
    }


    private function mustBeConcreteUser(UserInterface $u): User
    {
        if (!$u instanceof User) {
            $byEmail = $this->users->findOneBy(['email' => $u->getUserIdentifier()]);
            if (!$byEmail instanceof User) {
                throw new \RuntimeException('Utilisateur courant introuvable');
            }
            return $byEmail;
        }
        return $u;
    }


    private function explicitNull(object $input, string $field): bool
    {
        return method_exists($input, 'hasKey') && $input->hasKey($field) && $input->{"get" . ucfirst($field)}() === null;
    }

    /**
     * @param int $id
     * @param UserInterface $currentUser
     * @return array
     */
    public function Verify(int $id, UserInterface $currentUser): array
    {
        $ticket = $this->tickets->find($id) ?? throw new \RuntimeException('Ticket introuvable');

        $currentId = $currentUser->getUserIdentifier();
        $isAdmin = $this->auth->isGranted('ROLE_ADMIN');
        $isAuthor = $ticket->getAuthor()?->getUserIdentifier() === $currentId;
        $isAssignee = $ticket->getAssignee()?->getUserIdentifier() === $currentId;

        if (!$isAdmin && !$isAuthor && !$isAssignee) {
            throw new \RuntimeException('Accès refusé');
        }
        return array($ticket, $isAdmin, $isAuthor, $isAssignee);
    }
}
