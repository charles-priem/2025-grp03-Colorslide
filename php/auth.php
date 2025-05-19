<?php
session_start();
require_once 'db.php';
require_once __DIR__ . '/../mail/mail.php';

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // --- INSCRIPTION ---
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pwd = $_POST['password'] ?? '';

        // Vérifications de base
        if (empty($username)) $errors['username'] = "Nom d'utilisateur requis";
        if (empty($email)) $errors['email'] = "Email requis";
        if (empty($pwd)) $errors['password'] = "Mot de passe requis";

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors['email'] = "Email déjà utilisé";

        if (empty($errors)) {
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
            $validation_code = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_verified, validation_code) VALUES (?, ?, ?, 0, ?)");
            if ($stmt->execute([$username, $email, $hashedPwd, $validation_code])) {
                $user_id = $pdo->lastInsertId();
                sendValidationMail($email, $user_id, $validation_code);
                $success = "Registration successful! A validation email has been sent to you.";
            } else {
                $errors['global'] = "Erreur lors de l'inscription.";
            }
        }
    }

    // --- CONNEXION ---
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = trim($_POST['email'] ?? '');
        $pwd = $_POST['password'] ?? '';

        if (empty($email)) $errors['email'] = "Email requis";
        if (empty($pwd)) $errors['password'] = "Mot de passe requis";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($pwd, $user['password'])) {
                if (!$user['is_verified']) {
                    $errors['global'] = "Votre compte n'est pas validé. Vérifiez vos emails.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['username'];
                    header('Location: dashboard.php');
                    exit;
                }
            } else {
                $errors['global'] = "Identifiants incorrects";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Color Slide - Authentication</title>
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
            <!-- Login Form -->
            <div class="login-box" id="login-box">
                <form action="" method="post" autocomplete="off">
                    <h2>Sign in</h2>

                    <?php 
                    if(($_POST['action'] ?? '') === 'login'):
                        if (isset($errors['global'])):
                            echo'<p class="error">' . $errors['global'] . '</p>';

                        elseif (isset($errors['email'])): 
                            echo'<p class="error">' . $errors['email'] . '</p>';

                        elseif (isset($errors['password'])): 
                            echo'<p class="error">' . $errors['password'] . '</p>';
                        endif;
                    endif;
                    ?>

                    <input type="hidden" name="action" value="login">
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="email" placeholder="" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                        <input type="password" name="password" placeholder="" required>
                        <label>Password</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox" name="remember">Remember me</label>
                        <a href="recovery.php">Forgot password?</a>
                    </div>
                    <button type="submit">Sign in</button>
                    <div class="register-link">
                        <p>Don't have an account? <a href="#" id="show-register">Register</a></p>
                    </div>
                </form>
            </div>
            <!-- Register Form -->
            <div class="login-box" id="register-box">
                <form action="" method="post" autocomplete="off">
                    <h2>Register</h2>
                    <?php if (($_POST['action'] ?? '') === 'register'): ?>
                        <?php
                        if (isset($errors['global']))         echo '<p class="error">' . $errors['global'] . '</p>';
                        elseif ($success)                     echo '<p class="success">' . $success . '</p>';
                        elseif (isset($errors['username']))   echo '<p class="error">' . $errors['username'] . '</p>';
                        elseif (isset($errors['email']))      echo '<p class="error">' . $errors['email'] . '</p>';
                        elseif (isset($errors['password']))   echo '<p class="error">' . $errors['password'] . '</p>';
                        ?>
                    <?php endif; ?>
                    <input type="hidden" name="action" value="register">
                    <div class="input-box">
                        <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                        <input type="text" name="username" placeholder="" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        <label>Username</label>
                    </div>

                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="email" placeholder="" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <label>Email</label>
                    </div>

                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                        <input type="password" name="password" placeholder="" required>
                        <label>Password</label>
                    </div>

                    <button type="submit">Register</button>
                    <div class="register-link">
                        <p>Already have an account? <a href="#" id="show-login">Sign in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<footer>
    <?php require_once "footer.php"; ?>
</footer>
<script>
const loginBox = document.getElementById('login-box');
const registerBox = document.getElementById('register-box');
const showRegister = document.getElementById('show-register');
const showLogin = document.getElementById('show-login');
const goLogin = document.getElementById('go-login');

function resetBoxes() {
    loginBox.classList.remove('slide-in-left', 'slide-out-left', 'slide-in-right', 'slide-out-right');
    registerBox.classList.remove('slide-in-left', 'slide-out-left', 'slide-in-right', 'slide-out-right');
}

document.body.classList.add('no-anim');
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.body.classList.remove('no-anim');
    }, 50);

    <?php if (isset($_POST['action']) && $_POST['action'] === 'register'): ?>
        resetBoxes();
        loginBox.classList.add('slide-out-left');
        registerBox.classList.add('slide-in-right');
    <?php else: ?>
        resetBoxes();
        registerBox.classList.add('slide-out-right');
        loginBox.classList.add('slide-in-left');
    <?php endif; ?>
});

showRegister?.addEventListener('click', e => {
    e.preventDefault();
    document.body.classList.remove('no-anim');
    resetBoxes();
    loginBox.classList.add('slide-out-left');
    registerBox.classList.add('slide-in-right');
});

showLogin?.addEventListener('click', e => {
    e.preventDefault();
    document.body.classList.remove('no-anim');
    resetBoxes();
    registerBox.classList.add('slide-out-right');
    loginBox.classList.add('slide-in-left');
});

goLogin?.addEventListener('click', e => {
    e.preventDefault();
    document.body.classList.remove('no-anim');
    resetBoxes();
    registerBox.classList.add('slide-out-right');
    loginBox.classList.add('slide-in-left');
});
</script>

</body>
</html>