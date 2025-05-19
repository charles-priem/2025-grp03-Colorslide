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
        // Redirection avec succès
        header("Location: ../auth.php?verified=1");
        exit;
    } else {
        // Redirection avec erreur
        header("Location: ../auth.php?verified=0");
        exit;
    }
} else {
    // Redirection avec erreur de paramètres
    header("Location: ../auth.php?verified=0");
    exit;
}