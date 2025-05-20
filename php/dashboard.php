<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$success = '';
$error = '';

require_once 'db.php'; 

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $password = $user['password']; // Par défaut, on garde l'ancien

        // Validation du mot de passe si modifié
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) {
                $error = "Password must be at least 8 characters.";
            } elseif (!preg_match('/[0-9]/', $newPassword)) {
                $error = "Password must contain at least one digit.";
            } elseif (!preg_match('/[a-zA-Z]/', $newPassword)) {
                $error = "Password must contain at least one letter.";
            } else {
                $password = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }
        if(!$error) {
            /* Avatar */
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2 MB

                if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                    throw new Exception("File type not allowed. Only JPEG, PNG, and GIF are accepted.");
                }

                if ($_FILES['avatar']['size'] > $maxFileSize) {
                    throw new Exception("File size exceeds the 2 MB limit.");
                }

                $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
            } else {
                $avatar = $user['avatar'];
            }

            $update = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, avatar = ? WHERE id = ?");
            $update->execute([$username, $email, $password, $avatar, $_SESSION['user_id']]);

            $success = "Profile updated successfully!";
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Dashboard - Color Slide</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="connexion">
<header>
  <?php require_once "header1.php"; ?>
</header>
<main id="Dashboard">
    <section>
      <div class="dashboard-box" id="Profile">
            <div id="profile-view">
                <div class="profile-photo">
                    <img src="<?php echo htmlspecialchars($photo); ?>"
                         alt="Profile picture"
                         class="dashboard-avatar-img">
                </div>
                <div class="profile-field">
                    <span class="profile-label">Nom d'utilisateur :</span>
                    <span id="username-value"><?= htmlspecialchars($user['username'] ?? '') ?></span>
                    <button class="edit-field-btn" data-field="username" type="button">Modifier</button>
                    <input class="edit-field-input" id="username-input" type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" style="display:none;">
                </div>
                <div class="profile-field">
                    <span class="profile-label">Email :</span>
                    <span id="email-value"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                    <button class="edit-field-btn" data-field="email" type="button">Modifier</button>
                    <input class="edit-field-input" id="email-input" type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" style="display:none;">
                </div>
                <div class="profile-field">
                    <span class="profile-label">Mot de passe :</span>
                    <span>********</span>
                    <button class="edit-field-btn" data-field="password" type="button">Modifier</button>
                    <input class="edit-field-input" id="password-input" type="password" placeholder="Nouveau mot de passe" style="display:none;">
                </div>
                <div class="profile-field">
                    <span class="profile-label">Photo :</span>
                    <button class="edit-field-btn" data-field="avatar" type="button">Changer</button>
                    <input class="edit-field-input" id="avatar-input" type="file" name="avatar" accept="image/*" style="display:none;">
                </div>
                <div id="profile-actions" style="display:none;">
                    <button id="save-profile-btn" type="button">Enregistrer</button>
                    <button id="cancel-profile-btn" type="button">Annuler</button>
                </div>
                <form method="post" action="logout.php" style="margin-top:10px;">
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
        </div>
    </section>
</main>
<footer>
    <?php
        require_once "footer.php";
    ?>
</footer>

<div class="cursor" id="cursor"></div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="../script.js"></script>
<script>
const fields = ['username', 'email', 'password', 'avatar'];
let editing = false;

document.querySelectorAll('.edit-field-btn').forEach(btn => {
    btn.onclick = function() {
        const field = btn.dataset.field;
        document.getElementById(field + '-value')?.style.display = 'none';
        btn.style.display = 'none';
        document.getElementById(field + '-input').style.display = 'inline-block';
        document.getElementById('profile-actions').style.display = 'flex';
        editing = true;
    };
});

document.getElementById('cancel-profile-btn').onclick = function() {
    fields.forEach(field => {
        document.getElementById(field + '-input').style.display = 'none';
        document.getElementById(field + '-value')?.style.display = '';
        document.querySelector('.edit-field-btn[data-field="'+field+'"]').style.display = '';
    });
    document.getElementById('profile-actions').style.display = 'none';
    editing = false;
};

// Pour la démo, tu peux ajouter la logique d'envoi AJAX ici ou faire un submit classique
document.getElementById('save-profile-btn').onclick = function() {
    // Tu peux ici faire un submit AJAX ou afficher le formulaire d'édition complet
    document.getElementById('profile-edit-form').submit();
};
</script>
</body>
</html>