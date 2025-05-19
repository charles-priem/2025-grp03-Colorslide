<?php
// Import des classes PHPMailer dans l'espace de noms global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chargement de l'autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// Connexion à la base de données
require_once __DIR__ . '/../php/db.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Vérifier que les variables d'environnement sont bien chargées
if (!isset($_ENV['EMAIL_USERNAME']) || !isset($_ENV['EMAIL_PASSWORD'])) {
    die('Les variables d\'environnement ne sont pas correctement définies');
}

function sendRecoveryMail($to, $user_id) {
    global $pdo;

    // Générer un code à 6 chiffres
    $code = rand(100000, 999999);

    // Enregistrer le code dans la table recuperation
    $stmt = $pdo->prepare("INSERT INTO recuperation (user_id, code) VALUES (?, ?)");
    $stmt->execute([$user_id, $code]);

    // Envoi du mail
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USERNAME'];
        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom($_ENV['EMAIL_USERNAME'], 'Support');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Password reset request';
        $mail->Body    = "Your recovery code is : <b>$code</b>";
        $mail->AltBody = "Your recovery code is : $code";
        $mail->send();
        // echo 'Le message a été envoyé'; // Optionnel, à commenter pour éviter l'affichage lors de la redirection
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
    }
}