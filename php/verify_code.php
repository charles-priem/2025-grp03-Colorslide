<?php
session_start();
require_once __DIR__ . '/../php/db.php';

$success = "";
$error = "";
$email = $_GET['email'] ?? ($_POST['email'] ?? '');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $code = $_POST['code'] ?? '';

    if ($email && $code) {
        $stmt = $pdo->prepare("
        SELECT r.* FROM recuperation r
        JOIN users u ON r.user_id = u.id
        WHERE u.email = ? AND r.code = ?
    ");
    $stmt->execute([$email, $code]);
    $row = $stmt->fetch();

        if ($row) {
            $_SESSION['recovery_email'] = $email;
            header('Location: reset_password.php');
            exit;
        } else {
            $error = "The code is incorrect or the email does not match.";
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
    <title>Color Slide - Password Recovery</title>
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
                    <h2>Vérification du code</h2>
                    <?php if ($success): ?>
                        <p class="success"><?= htmlspecialchars($success) ?></p>
                    <?php elseif ($error): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="email" placeholder="" value="<?= htmlspecialchars($email) ?>" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="key-outline"></ion-icon></span>
                        <input type="text" name="code" required placeholder="">
                        <label>Code</label>
                    </div>
                    <button type="submit">Validate</button>
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