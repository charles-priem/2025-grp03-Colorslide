<?php
session_start();

$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pwd, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Color Slide - Connexion</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="connexion">
<header>
    <?php require_once "header1.php"; ?>
</header>

<main id="connexion-main">
    <section>
        <!-- <video autoplay muted loop id="background-video">
            <source src="../images/video.mp4" type="video/mp4">
        </video> -->
        <div class="form-wrapper">
            <div class="login-box" id="login">
                <form action="" method="post">
                    <h2>Login</h2>

                    <?php if ($error): ?>
                        <p class="error"><?= $error ?></p>
                    <?php endif; ?>

                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>

                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>

                    <div class="remember-forgot">
                        <label><input type="checkbox" name="remember">Remember me</label>
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit">Login</button>
                    <div class="register-link">
                        <p>Don't have an account? <a href="inscription.php">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
