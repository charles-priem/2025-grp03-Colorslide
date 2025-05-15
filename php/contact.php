<?php
$successMessage = "";
$errorMessage = "";

$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';

session_start();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? '');
    $message = trim($_POST["message"] ?? '');
    $fileName = null;
  $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id']:NULL;
    if (empty($subject) || empty($message)) {
        $errorMessage = "Subject and message are required.";
    }

    if (empty($errorMessage) && !empty($_FILES["document"]["name"])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $originalName = basename($_FILES["document"]["name"]);
        $fileName = uniqid() . "_" . $originalName;
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES["document"]["tmp_name"], $uploadPath)) {
            $errorMessage = "Erreur lors de l'envoi du fichier.";
        }
    }

    if (empty($errorMessage)) {
        $stmt = $pdo->prepare("INSERT INTO contact (user_id, subject, message, file) VALUES (:user_id, :subject, :message, :file)");
        $stmt->execute([
            ":user_id" => $user_id,
            ":subject" => $subject,
            ":message" => $message,
            ":file" => $fileName
        ]);
        $successMessage = "Votre message a été envoyé avec succès.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Space Grotesk'}
    .message-success { color: #4CAF50; text-align: center; margin-top: 10px; }
    .message-error { color: #f44336; text-align: center; margin-top: 10px; }
  </style>
</head>
<body class="contact">
<header>
  <?php require_once "header1.php"; ?>
</header>

<main id="contact">
  <form action="contact.php" method="post" enctype="multipart/form-data">
    <h2>Contact</h2>

    <?php if ($successMessage): ?>
      <p class="message-success"><?= htmlspecialchars($successMessage) ?></p>
    <?php elseif ($errorMessage): ?>
      <p class="message-error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <div class="input-box">
      <select name="subject" id="subject" required>
        <option value="" disabled <?= empty($_POST['subject']) ? 'selected' : '' ?> hidden>Select a subject</option>
        <option value="suggestion" <?= (($_POST['subject'] ?? '') === 'suggestion') ? 'selected' : '' ?>>Suggestions</option>
        <option value="complaint" <?= (($_POST['subject'] ?? '') === 'complaint') ? 'selected' : '' ?>>Complaints</option>
        <option value="registration" <?= (($_POST['subject'] ?? '') === 'registration') ? 'selected' : '' ?>>Registration</option>
        <option value="issue" <?= (($_POST['subject'] ?? '') === 'issue') ? 'selected' : '' ?>>System issue</option>
        <option value="technical" <?= (($_POST['subject'] ?? '') === 'technical') ? 'selected' : '' ?>>Technical problem</option>
      </select>
    </div>

    <div class="input-box">
      <textarea name="message" id="message" rows="5" maxlength="200" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      <label for="message">Your message</label>
    </div>

    <label for="document" class="custom-file-upload">Choose a file</label>
    <input type="file" id="document" name="document">

    <button type="submit">Send</button>
    <button type="reset">Reset</button>
  </form>
</main>

<footer>
  <?php include 'footer.php'; ?>
</footer>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>