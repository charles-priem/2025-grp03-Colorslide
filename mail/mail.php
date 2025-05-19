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

// Envoi du mail de validation de compte
function sendValidationMail($to, $user_id, $code) {
    global $pdo;

    // Enregistrer le code dans la table users
    $stmt = $pdo->prepare("UPDATE users SET validation_code = ?, is_verified = 0 WHERE id = ?");
    $stmt->execute([$code, $user_id]);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USERNAME'];
        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($_ENV['EMAIL_USERNAME'], 'Support');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Validate your Color Slide account';
        $mail->Body    = "Click on this link to validate your account: <a href='http://localhost/2025-grp03/php/verify_account.php/?email=$to&code=$code'>Valider mon compte</a>";
        $mail->AltBody = "Validate your account with this code : $code";
        $mail->send();
    } catch (Exception $e) {
        echo "Erreur d'envoi du mail : " . $mail->ErrorInfo;
    }
}

// Envoi du mail de récupération
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
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
    }
}