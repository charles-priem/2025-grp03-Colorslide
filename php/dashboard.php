
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$success = '';
$error = '';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bddgrp03colorslide;charset=utf8', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

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
                throw new Exception("File type not allowed. Only JPEG, PNG, and GIF are accepted.");
            }

            if ($_FILES['avatar']['size'] > $maxFileSize) {
                throw new Exception("File size exceeds the 2 MB limit.");
            }

            $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
        } else {
            $avatar = $user['avatar'];
        }

        $update = $pdo->prepare("UPDATE users SET name = ?, firstname = ?, mail = ?, password = ?, avatar = ? WHERE id = ?");
        $update->execute([$name, $firstname, $mail, $password, $avatar, $_SESSION['user_id']]);

        $success = "Profile updated successfully!";
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
                <?php elseif ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
                    <label>First Name</label>
                </div>

                <div class="input-box">
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    <label>Last Name</label>
                </div>

                <div class="input-box">
                    <input type="email" name="mail" value="<?= htmlspecialchars($user['mail'] ?? '') ?>" required>
                    <label>Email</label>
                </div>

                <div class="input-box">
                    <input type="password" name="password">
                    <label>New password (leave blank to keep current)</label>
                </div>

                <button type="submit">Save</button>
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

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>