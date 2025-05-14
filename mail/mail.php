<?php
// Import des classes PHPMailer dans l'espace de noms global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chargement de l'autoloader de Composer
require 'vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Vérifier que les variables d'environnement sont bien chargées
if (!isset($_ENV['EMAIL_USERNAME']) || !isset($_ENV['EMAIL_PASSWORD'])) {
    die('Les variables d\'environnement ne sont pas correctement définies');
}

function sendRecoveryMail($to, $code) {
    // Création d'une instance ; passer true active les exceptions
    $mail = new PHPMailer(true);

    try {
        // Paramètres du serveur
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USERNAME'];
        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Options du certificat
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Destinataires
        $mail->setFrom('clement.rubin76@gmail.com', 'Support');
        $mail->addAddress($to);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Demande de reinitialisation du mot de passe';
        $mail->Body    = "Votre code de recuperation est : <b>$code</b>";
        $mail->AltBody = "Votre code de recuperation est    : $code";

        $mail->send();
        echo 'Le message a été envoyé';
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
    }
}


// Test manuel : à supprimer ou adapter ensuite
if (isset($_GET['test'])) {
    // Mets ici ton adresse mail pour tester
    sendRecoveryMail('delattre.gauthier1@gmail.com', rand(100000, 999999));
}