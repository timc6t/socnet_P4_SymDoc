<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Symfony\Component\Security\Http\Attribute\IsGranted;

class LoginController extends AbstractController {
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/login', name: 'socnet_login')]
    public function login(Request $request /*, UserPasswordEncoderInterface $passwordEncoder*/): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $userRepository = $this->doctrine->getRepository(User::class);
            $user = $userRepository->findOneBy(['username' => $username]);

            if ($user /*&& $passwordEncoder->isPasswordValid*/($user /*, $password*/)) {
                $this->addFlash('success', 'Welcome back!');
                return $this->redirectToRoute('dashboard');
            } else {
                $this->addFlash('error', 'Invalid username or password.');
            }
        }
        return $this->render('login.html.twig');
    }
}