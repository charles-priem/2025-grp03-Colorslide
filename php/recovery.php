<?php
session_start();
require_once __DIR__ . '/../mail/mail.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        require_once 'db.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Appelle la fonction ici, en passant l'email et l'id utilisateur
            sendRecoveryMail($email, $user['id']);
            header("Location: verify_code.php?email=" . urlencode($email));
            exit;
        } else {
            $error = "This email address is not registered";
        }
    } else {
        $error = "Please enter a valid email address";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Color Slide - Récupération de mot de passe</title>
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
            <div class="login-box" id="recovery-box">
                <form action="" method="post" autocomplete="off">
                    <h2>Password Recovery</h2>
                    
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="email" required placeholder="">
                        <label>Email</label>
                    </div>
                    <?php if ($success): ?>
                        <p class="success"><?= htmlspecialchars($success) ?></p>
                    <?php elseif ($error): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <button type="submit">Send the code</button>
                </form>
            </div>
        </div>
    </section>
</main>
<footer>
    <?php include '../php/footer.php'; ?>
</footer>
</body>
</html>