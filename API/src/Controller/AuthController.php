<?php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AbstractController
{
    private $jwtManager;
    private $tokenStorage;

    public function __construct(JWTManager $jwtManager, TokenStorageInterface $tokenStorage)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/login', name: 'login_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('songs.html.twig');
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function api_login(Request $request): JsonResponse
    {
        if ($this->getUser()) {
            return $this->json([
                'message' => 'Vous êtes déjà connecté.'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $email = $request->get('email');
        $password = $request->get('password');

        $user = $this->getUser();

        if ($user) {
            $token = $this->jwtManager->create($user);
            return $this->json([
                'token' => $token
            ]);
        }

        return $this->json([
            'message' => 'Identifiants invalides.'
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
