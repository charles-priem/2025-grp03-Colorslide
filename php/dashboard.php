<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php.php");
    exit();
}

$success = '';
$error = '';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bddgrp03colorslide;charset=utf8', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupère les infos actuelles de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Utilisateur non trouvé.");
    }

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $mail = $_POST['mail'] ?? '';
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

        // Avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
        } else {
            $avatar = $user['avatar'];
        }

        // Update
        $update = $pdo->prepare("UPDATE users SET name = ?, mail = ?, password = ?, avatar = ? WHERE id = ?");
        $update->execute([$name, $mail, $password, $avatar, $_SESSION['user_id']]);

        $success = "Profil mis à jour avec succès !";
        // Recharge les nouvelles données
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Color Slide</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="connexion">
<header>
    <?php require_once "header1.php"; ?>
</header>

<main id="connexion-main">
    <section>
        <video autoplay muted loop id="background-video">
            <source src="../images/video.mp4" type="video/mp4">
        </video>

        <div class="login-box" id="login">
            <form method="POST" enctype="multipart/form-data">
                <h2>Mon profil</h2>

                <?php if ($success): ?>
                    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
                <?php elseif ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!-- Affichage Avatar -->
                <?php if (!empty($user['avatar'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($user['avatar']) ?>" alt="Avatar" width="100" style="margin-bottom:10px;">
                <?php endif; ?>

                <div class="input-box">
                    <label>Changer l'avatar</label>
                    <input type="file" name="avatar" accept="image/*">
                </div>

                <div class="input-box">
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    <label>Nom</label>
                </div>

                <div class="input-box">
                    <input type="email" name="mail" value="<?= htmlspecialchars($user['mail']) ?>" required>
                    <label>Email</label>
                </div>

                <div class="input-box">
                    <input type="password" name="password">
                    <label>Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                </div>

                <button type="submit">Sauvegarder</button>
                <a href="logout.php"><button type="button">Se déconnecter</button></a>
            </form>
        </div>
    </section>
</main>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
