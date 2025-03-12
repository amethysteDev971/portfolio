<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
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
            ->from('no-reply@amethyste-design.fr')
            ->to('mgaloyer@uneak.fr')
            ->subject('Test Mailjet via Symfony Mailer')
            ->text('Bonjour, ceci est un test d’envoi via Mailjet !')
            ->html('<p>Bonjour, ceci est un <strong>test</strong> d’envoi via Mailjet !</p>');

        $mailer->send($email);

        return new Response('Email envoyé avec succès !');
    }
    #[Route('/send-email2', name: 'send_email')]
    public function sendEmail2(): Response
    {
        // Remplacez par vos identifiants Mailjet
        $dsn = 'mailjet+api://1bd4369d5430028dfa159e801027ca80:6104c23e487f2fafd01d04c8317925f9@default';

        // Créez le transport à partir de la DSN
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        // Construisez l'email
        $email = (new Email())
            ->from('no-reply@amethyste-design.fr')
            ->to('mgaloyer@uneak.fr')
            ->subject('Test minimal Mailjet')
            ->text('Ceci est un test minimal d\'envoi d\'email via Mailjet API.');

        // Envoi et mesure du temps d'exécution
        $start = microtime(true);
        try {
            $mailer->send($email);
            echo "Email envoyé avec succès !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . $e->getMessage() . "\n";
        }
        $end = microtime(true);
        echo "Temps d'exécution : " . ($end - $start) . " secondes\n";
        die();
    }
}
