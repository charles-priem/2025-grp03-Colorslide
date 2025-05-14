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
    $firstname = $_POST['FirstName'] ?? '';
    $lastname = $_POST['LastName'] ?? '';
    $mail = $_POST['Email'] ?? '';
    $pwd = $_POST['password'] ?? '';

    if (!empty($firstname) && !empty($lastname) && !empty($mail) && !empty($pwd)) {
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
        // avatar est NULL par défaut
        $stmt = $pdo->prepare("INSERT INTO users (firstname, name, mail, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$firstname, $lastname, $mail, $hashedPwd])) {
            $success = "Registration successful! <a href='connexion.php'>Sign in</a>";
        } else {
            $error = "Registration error.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Color Slide - Register</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="connexion">
<header>
    <?php require_once "header1.php"; ?>
</header>

<main id="connexion-main">
    <section>
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
                    <input type="text" name="FirstName" required>
                    <label>First Name</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="LastName" required>
                    <label>Last Name</label>
                </div>

                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="Email" required>
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