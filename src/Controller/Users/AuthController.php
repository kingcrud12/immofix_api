<?php
// src/Controller/AuthController.php
namespace App\Controller\Users;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Users')]
class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload(
        )] RegisterDto $dto,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        MailService $mailer
    ): JsonResponse {
        $user = new User();
        $user->setFirstname($dto->firstname);
        $user->setLastname($dto->lastname);
        $user->setEmail($dto->email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $dto->password));

        $em->persist($user);
        $em->flush();

        $mailer->sendWelcomeEmail($user->getEmail(), $user->getFirstname() ?: $user->getEmail());

        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ], 201);
    }
}

