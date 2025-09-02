<?php
// src/Controller/MeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser(); // alimenté par le JWT authenticator
        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        return $this->json($user, 200, [], ['groups' => ['user:read']]);
    }
}
