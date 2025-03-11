<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HomeController.php',
        ]);
    }


    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('	no-reply@amethyste-design.fr')
            ->to('mgaloyer@uneak.fr')
            ->subject('Test Mailjet via Symfony Mailer')
            ->text('Bonjour, ceci est un test d’envoi via Mailjet !')
            ->html('<p>Bonjour, ceci est un <strong>test</strong> d’envoi via Mailjet !</p>');

        $mailer->send($email);

        return new Response('Email envoyé avec succès !');
    }
}
