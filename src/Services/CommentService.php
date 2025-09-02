<?php
// src/Services/CommentService.php
namespace App\Services;

use App\Entity\Comment;
use App\Entity\User;
use App\Interfaces\CommentInput;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentService implements CommentServiceInterface
{
    public function __construct(
        private EntityManagerInterface        $em,
        private CommentRepository             $comments,
        private TicketRepository              $tickets,
        private UserRepository                $users,
        private AuthorizationCheckerInterface $auth
    ) {}

    /** Création: auteur = current user par défaut, attaché au ticket fourni */
    public function createComment(CommentInput $input,int $id, UserInterface $currentUser): Comment
    {
        $author = $this->mustBeConcreteUser($currentUser);

        $ticket = $this->tickets->find($id)
            ?? throw new \RuntimeException('Ticket introuvable');

        $comment = new Comment();
        $comment->setText($input->getText() ?? '');
        $comment->setAuthor($author);
        $comment->setTicketId($ticket);


        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    /** Mise à jour: admin OU auteur du commentaire */
    public function updateComment(int $id, int $ticketId, CommentInput $input, UserInterface $currentUser): Comment
    {
        $ticket = $this->tickets->find($ticketId)
            ?? throw new \RuntimeException('Ticket introuvable');

        $comment = $this->comments->find($id)
            ?? throw new \RuntimeException('Commentaire introuvable');

        $comment = $this->comments->find($id)
            ?? throw new \RuntimeException('Commentaire introuvable');

        if (
            !$this->auth->isGranted('ROLE_ADMIN') &&
            $comment->getAuthor()?->getUserIdentifier() !== $currentUser->getUserIdentifier()
        ) {
            throw new \RuntimeException('Accès refusé');
        }

        if (($t = $input->getText()) !== null) {
            $comment->setText($t);
        }

        $this->em->flush();

        return $comment;
    }

    /** Suppression: admin OU auteur du commentaire */
    public function deleteComment(int $id, int $ticketId, UserInterface $currentUser): void
    {

        $ticket = $this->tickets->find($ticketId)
            ?? throw new \RuntimeException('Ticket introuvable');

        $comment = $this->comments->find($id)
            ?? throw new \RuntimeException('Commentaire introuvable');

        if (
            !$this->auth->isGranted('ROLE_ADMIN') &&
            $comment->getAuthor()?->getUserIdentifier() !== $currentUser->getUserIdentifier()
        ) {
            throw new \RuntimeException('Accès refusé');
        }

        $this->em->remove($comment);
        $this->em->flush();
    }

    /** Lecture publique d’un commentaire */
    public function getComment(int $id, int $ticketId): Comment
    {
        $ticket = $this->tickets->find($ticketId)
            ?? throw new \RuntimeException('Ticket introuvable');

        return $this->comments->find($id)
            ?? throw new \RuntimeException('Commentaire introuvable');
    }

    /** Lecture publique: tous les commentaires d’un ticket */
    public function getCommentsForTicket(int $ticketId): array
    {
        $ticket = $this->tickets->find($ticketId)
            ?? throw new \RuntimeException('Ticket introuvable');

        return $this->comments->createQueryBuilder('c')
            ->andWhere('c.ticket = :t')
            ->setParameter('t', $ticket)
            ->orderBy('c.id', 'ASC') // ou createdAt si tu as un timestamp
            ->getQuery()
            ->getResult();
    }

    // ------------------- Helpers -------------------


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
}
