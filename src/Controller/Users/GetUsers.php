<?php
// src/Controller/MeController.php
namespace App\Controller\Users;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[OA\Tag(name: 'Users')]
class GetUsers extends AbstractController
{
    public function __construct(
        private AuthorizationCheckerInterface $auth,
        private UserRepository $users
    ){}
    #[Route('/api/users', name: 'api_users', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $users = $this->users->findAll();

        if (!$users) {
            return $this->json(['message' => 'Aucun utilisateur enregistre '], 401);
        }

        $isAdmin = $this->auth->isGranted('ROLE_ADMIN');

        if (!$isAdmin) {
            return $this->json(['message' => 'Vous ne pouvez pas acceder a cette ressource'], 401);
        }

        return $this->json($users, 200, [], ['groups' => ['user:read']]);
    }
}
