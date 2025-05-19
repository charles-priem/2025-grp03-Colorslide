<?php
require_once 'db.php';

$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';

if ($email && $code) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND validation_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();
    if ($user) {
        $pdo->prepare("UPDATE users SET is_verified = 1, validation_code = NULL WHERE id = ?")->execute([$user['id']]);
        echo "Votre compte a été validé. <a href='auth.php'>Connectez-vous</a>";
    } else {
        echo "Lien invalide ou déjà utilisé.";
    }
} else {
    echo "Paramètres manquants.";
}