<?php
session_start();
require_once __DIR__ . '/../php/db.php';

if (!isset($_SESSION['recovery_email'])) {
    header('Location: verify_code.php');
    exit;
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pwd = $_POST['password'] ?? '';
    $pwd2 = $_POST['password2'] ?? '';

    if ($pwd && $pwd2) {
        if ($pwd !== $pwd2) {
            $error = "Passwords do not match.";
        } elseif (strlen($pwd) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif (!preg_match('/[0-9]/', $pwd)) {
            $error = "Password must contain at least one digit.";
        } elseif (!preg_match('/[a-zA-Z]/', $pwd)) {
            $error = "Password must contain at least one letter.";
        } else {
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPwd, $_SESSION['recovery_email']]);
            $stmt = $pdo->prepare("DELETE FROM recuperation WHERE user_id = (SELECT id FROM users WHERE email = ?)");
            $stmt->execute([$_SESSION['recovery_email']]);
            unset($_SESSION['recovery_email']);
            $success = "Password changed successfully. Redirecting to login...";
            echo "<meta http-equiv='refresh' content='3;url=auth.php'>";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="connexion">
<header>
    <?php require_once "header1.php"; ?>
</header>
<main id="connexion-main">
    <section>
        <div class="form-wrapper">
            <div class="login-box">
                <form action="" method="post">
                    <h2>Nouveau mot de passe</h2>
                    <?php if ($success): ?>
                        <p class="success"><?= $success ?></p>
                    <?php elseif ($error): ?>
                        <p class="error"><?= $error ?></p>
                    <?php endif; ?>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                        <input type="password" name="password" required placeholder="">
                        <label>Nouveau mot de passe</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                        <input type="password" name="password2" required placeholder="">
                        <label>Confirmer le mot de passe</label>
                    </div>
                    <button type="submit">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </section>
</main>
</body>
</html>