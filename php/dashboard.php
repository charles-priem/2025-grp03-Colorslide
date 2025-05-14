<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
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
        $firstname = $_POST['firstname'] ?? '';
        $mail = $_POST['mail'] ?? '';
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

        // Avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2 MB

            if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                throw new Exception("Le type de fichier n'est pas autorisé. Seuls les fichiers JPEG, PNG et GIF sont acceptés.");
            }

            if ($_FILES['avatar']['size'] > $maxFileSize) {
                throw new Exception("La taille du fichier dépasse la limite autorisée de 2 Mo.");
            }

            $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
        } else {
            $avatar = $user['avatar'];
        }

        // Update
        $update = $pdo->prepare("UPDATE users SET name = ?, firstname = ?, mail = ?, password = ?, avatar = ? WHERE id = ?");
        $update->execute([$name, $firstname, $mail, $password, $avatar, $_SESSION['user_id']]);

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

<main id="Dashboard">
    <section>
        <video autoplay muted loop id="background-video">
            <source src="../images/video.mp4" type="video/mp4">
        </video>

        <div class="dashboard-box" id="Profile">
            <form method="POST" enctype="multipart/form-data">
                <h2>My Profile</h2>
                <div class="profile-photo" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 18px;">
                    <?php
                    // Affiche l'avatar par défaut si l'utilisateur n'a pas d'avatar
                    if (!empty($user['avatar'])) {
                        $photo = 'data:image/jpeg;base64,' . base64_encode($user['avatar']);
                    } else {
                        $photo = '../images/png-clipart-user-profile-computer-icons-login-user-avatars-monochrome-black.png';
                    }
                    ?>
                    <label for="avatar-upload" style="cursor:pointer;">
                        <img src="<?php echo $photo; ?>"
                             alt="Photo de profil"
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: none;">
                        <div style="text-align:center; color:#007bff; font-size:0.95em;">Changer l'avatar</div>
                    </label>
                    <input id="avatar-upload" type="file" name="avatar" accept="image/*" style="display:none;">
                </div>

                <?php if ($success): ?>
                    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
                <?php elseif ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
                    <label>Prénom</label>
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
            </form>

            <?php if (!empty($user['avatar'])): ?>
                <form action="delete_photo.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <button type="submit" name="delete_photo" style="color: white; border: none; padding: 10px 20px; cursor: pointer;">
                        Delete Avatars
                    </button>
                </form>
            <?php endif; ?>

            <form method="post" action="logout.php" style="margin-top:10px;">
                <button type="submit">Se déconnecter</button>
            </form>
        </div>
    </section>
</main>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>