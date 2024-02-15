<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController {
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/register', name: 'socnet_register')]
    
    public function register(Request $request/*, UserPasswordEncoderInterface $passwordEncoder*/): Response
    {
        $username = $password = $email = "";
        $username_err = $password_err = $email_err = "";

        // Manejar la solicitud de registro
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $email = $request->request->get('email');

            // Validar el nombre de usuario
            $userRepository = $this->doctrine->getRepository(User::class);
            $existingUser = $userRepository->findOneBy(['username' => $username]);
            if ($existingUser) {
                $username_err = "This username already exists.";
            }

            // Validar el correo electrónico
            if (empty($email)) {
                $email_err = "Please enter an email.";
            }

            // Validar la contraseña
            if (empty($password)) {
                $password_err = "Please enter a password.";
            } elseif (strlen($password) < 6) {
                $password_err = "Password must have at least 6 characters.";
            }

            // Si no hay errores de validación, registrar al usuario
            if (empty($username_err) && empty($password_err) && empty($email_err)) {
                $entityManager = $this->doctrine->getManager();
                $user = new User();
                $user->setUsername($username);
                /*$user->setPassword($passwordEncoder->encodePassword($user, $password));*/
                $user->setEmail($email);
                $entityManager->persist($user);
                $entityManager->flush();

                // Enviar correo de confirmación
                $mail = new PHPMailer(); // Error típico de PHPMailer
                // Configurar correo electrónico
                // ...
                if ($mail->send()) {
                    return $this->redirectToRoute('register_success');
                } else {
                    return new Response("Email could not be sent.");
                }
            }
        }

        // Renderizar el formulario de registro
        return $this->render('register.html.twig', [
            'username' => $username,
            'email' => $email,
            'username_err' => $username_err,
            'password_err' => $password_err,
            'email_err' => $email_err,
        ]);
    }
}
