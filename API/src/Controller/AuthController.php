<?php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class AuthController extends AbstractController
{
    private $jwtManager;
    private $session;

    public function __construct(JWTTokenManagerInterface $jwtManager, RequestStack $requestStack)
    {
        $this->jwtManager = $jwtManager;
        $this->session = $requestStack->getSession();
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }

    #[Route('/login', name: 'login_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function api_login(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): JsonResponse
    {
        if ($this->getUser()) {
            return $this->json([
                'message' => 'Vous êtes déjà connecté.'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email) {
            return $this->json([
                'message' => 'Veuillez saisir un email.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $doctrine->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json([
                'message' => 'Identifiants invalides (Email)'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'Identifiants invalides (Mot de passe)'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $jwt = $this->jwtManager->create($user);

        $this->session->set('token', $jwt);
        $this->session->set('username', $user->getUsername());

        return $this->json([
            'message' => 'Connecté avec succès. User ID: ' . $user->getId(),
            'token' => $jwt
        ], JsonResponse::HTTP_OK);

        // return new RedirectResponse('/');
    }

    #[Route('/api/token', name: 'get_token', methods: ['GET'])]
    public function getTokenFromSession(): JsonResponse
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $token = $this->session->get('token');
        $username = $this->session->get('username');

        if (!$token) {
            return $this->json([
                'message' => 'Aucun token trouvé. Veuillez vous connecter.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'token' => $token,
            'username' => $username,
        ]);
    }
}
