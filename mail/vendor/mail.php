<?php
// Import des classes PHPMailer dans l'espace de noms global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chargement de l'autoloader de Composer
require 'autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Création d'une instance ; passer `true` active les exceptions
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();                          // Envoi via SMTP
    $mail->Host       = 'smtp-relay.gmail.com';     // Serveur SMTP de Gmail
    $mail->SMTPAuth   = true;                 // Activer l'authentification SMTP
    $mail->Username   = 'clement.rubin76@gmail.com'; // Ton adresse e-mail Gmail
    $mail->Password   = 'Paulestbeau';       // Ton mot de passe Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Activer le chiffrement SSL
    $mail->Port       = 465;                // Port SMTP de Gmail

    // Destinataires
    $mail->setFrom('clement.rubin76@gmail.com', 'Mailer');
    $mail->addAddress('clement.rubin@student.junia.com', 'Paul');     // Ajouter un destinataire


    // Contenu
    $mail->isHTML(true);                                  // Définir le format du courriel en HTML
    $mail->Subject = 'Voici le sujet';
    $mail->Body    = 'Ceci est le corps du message HTML <b>en gras !</b>';
    $mail->AltBody = 'Ceci est le corps du message en texte brut pour les clients de messagerie non-HTML';

    $mail->send();
    echo 'Le message a été envoyé';
} catch (Exception $e) {
    echo "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
}
