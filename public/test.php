<?php

    require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Mailer\Transport;

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
