<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_custom_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // At top of login() method
        file_put_contents(__DIR__ . '/../../../var/log/debug-login.log', "Login hit at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        file_put_contents(__DIR__ . '/../../../var/log/debug-login.log', "Email: $email\n", FILE_APPEND);

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        file_put_contents(__DIR__ . '/../../../var/log/debug-login.log', "User found: " . ($user ? 'yes' : 'no') . "\n", FILE_APPEND);


        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password required'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$hasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                // Add more user fields if needed
            ]
        ]);
    }
}
