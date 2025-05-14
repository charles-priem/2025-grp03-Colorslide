<?php
$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $mail = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';

    if (!empty($name) && !empty($mail) && !empty($pwd)) {
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, mail, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $mail, $hashedPwd])) {
            $success = "Inscription réussie ! <a href='connexion.php'>Se connecter</a>";
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    } else {
        $error = "Tous les champs sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Color Slide - Inscription</title>
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
        <div class="login-box">
            <form action="" method="post">
                <h2>Register</h2>

                <?php if ($success): ?>
                    <p class="success"><?= $success ?></p>
                <?php elseif ($error): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="name" required>
                    <label>Name</label>
                </div>

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

                <button type="submit">Create your account</button>
            </form>
        </div>
    </section>
</main>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
