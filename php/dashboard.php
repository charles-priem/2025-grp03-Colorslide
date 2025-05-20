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
            <form method="POST" enctype="multipart/form-data">
                <h2>My Profile</h2>
                <div class="profile-photo" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 18px;">
                    <?php
                    if (!empty($user['avatar'])) {
                        $photo = 'data:image/jpeg;base64,' . base64_encode($user['avatar']);
                    } else {
                        $photo = '../images/png-clipart-user-profile-computer-icons-login-user-avatars-monochrome-black.png';
                    }
                    ?>
                    <label for="avatar-upload" style="cursor:pointer;">
                        <img src="<?php echo htmlspecialchars($photo); ?>"
                             alt="Profile picture"
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: none;">
                        <div class="ChangeProfilePicture">Change profile picture</div>
                    </label>
                    <input id="avatar-upload" type="file" name="avatar" accept="image/*" style="display:none;">
                </div>

                <?php if ($success): ?>
                    <p class="success"><?= htmlspecialchars($success) ?></p>
                <?php elseif ($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="profile-field">
                    <span class="profile-label">Nom d'utilisateur :</span>
                    <span id="username-value"><?= htmlspecialchars($user['username'] ?? '') ?></span>
                    <div id="username-inputbox" class="input-box" style="display:none;">
                        <input id="username-input" type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
                    </div>
                    <button class="edit-btn" data-field="username" type="button">
                        <ion-icon name="pencil-outline"></ion-icon>
                    </button>
                    <button class="save-btn" data-field="username" type="button" style="display:none;">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </button>
                </div>
                <div class="profile-field">
                    <span class="profile-label">Email :</span>
                    <span id="email-value"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                    <div id="email-inputbox" class="input-box" style="display:none;">
                        <input id="email-input" type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    <button class="edit-btn" data-field="email" type="button">
                        <ion-icon name="pencil-outline"></ion-icon>
                    </button>
                    <button class="save-btn" data-field="email" type="button" style="display:none;">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </button>
                </div>
                <div class="profile-field">
                    <span class="profile-label">Mot de passe :</span>
                    <span id="password-value">********</span>
                    <div id="password-inputbox" class="input-box" style="display:none;">
                        <input id="password-input" type="password" name="password" placeholder="Nouveau mot de passe">
                    </div>
                    <button class="edit-btn" data-field="password" type="button">
                        <ion-icon name="pencil-outline"></ion-icon>
                    </button>
                    <button class="save-btn" data-field="password" type="button" style="display:none;">
                        <ion-icon name="checkmark-outline"></ion-icon>
                    </button>
                </div>
            </form>

            <?php if (!empty($user['avatar'])): ?>
                <form action="delete_photo.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <button type="submit" name="delete_photo" >
                        Delete profile picture
                    </button>
                </form>
            <?php endif; ?>

            <form method="post" action="logout.php" style="margin-top:10px;">
                <button type="submit">Logout</button>
            </form>
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
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.onclick = function() {
        const field = btn.dataset.field;
        document.getElementById(field + '-value').style.display = 'none';
        document.getElementById(field + '-inputbox').style.display = 'inline-block';
        btn.style.display = 'none';
        document.querySelector('.save-btn[data-field="'+field+'"]').style.display = 'inline-block';
        if(field === "password") document.getElementById(field + '-input').value = "";
    };
});
document.querySelectorAll('.save-btn').forEach(btn => {
    btn.onclick = function() {
        const field = btn.dataset.field;
        document.getElementById(field + '-value').style.display = '';
        document.getElementById(field + '-inputbox').style.display = 'none';
        btn.style.display = 'none';
        document.querySelector('.edit-btn[data-field="'+field+'"]').style.display = 'inline-block';
        btn.closest('form').submit();
    };
});
</script>
</body>
</html>