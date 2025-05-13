<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body class="connexion">
    <header>
    <<?php require_once "header.php"; ?>
</header>
    <main id="connexion-main">>
    <div class="login-box">
        <form action="">
            <h2>Login</h2>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon>
                </span>
                <input type="email" required>
                <label>Email</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" required>
                <label>Password</label>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">remember me
        </label>
        <a href="#">Forgot password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="register-link">
            <p>Don't have an account?<a href="#">Register</a></p>
        </div>
        </form>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    


     
</body>
</html>
