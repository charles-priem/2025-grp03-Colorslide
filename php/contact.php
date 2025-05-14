<?php
// === Traitement du formulaire ===
$successMessage = "";
$errorMessage = "";

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root'; 
$password = 'root';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lastname = $_POST["lastname"] ?? '';
    $firstname = $_POST["firstname"] ?? '';
    $email = $_POST["email"] ?? '';
    $subject = $_POST["subject"] ?? '';
    $message = $_POST["message"] ?? '';
    $fileName = null;

    // Gestion du fichier
    if (!empty($_FILES["document"]["name"])) {
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

    // Insertion si pas d'erreur
    if (empty($errorMessage)) {
        $stmt = $pdo->prepare("INSERT INTO contact (email, lastname, firstname, subject, message, file)
                               VALUES (:email, :lastname, :firstname, :subject, :message, :file)");
        $stmt->execute([
            ":email" => $email,
            ":lastname" => $lastname,
            ":firstname" => $firstname,
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
        <input type="text" name="lastname" id="lastname" required>
        <label for="lastname">Last name</label>
      </div>

      <div class="input-box">
        <input type="text" name="firstname" id="firstname" required>
        <label for="firstname">First name</label>
      </div>

      <div class="input-box">
        <input type="email" name="email" id="email" required>
        <label for="email">Email</label>
      </div>

      <div class="input-box">
        <select name="subject" id="subject" required>
          <option value="" disabled selected hidden>Select a subject</option>
          <option value="suggestion">Suggestions</option>
          <option value="complaint">Complaints</option>
          <option value="registration">Registration</option>
          <option value="issue">System issue</option>
          <option value="technical">Technical problem</option>
        </select>
      </div>

      <div class="input-box">
        <textarea name="message" id="message" rows="5" maxlength="200" required></textarea>
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
