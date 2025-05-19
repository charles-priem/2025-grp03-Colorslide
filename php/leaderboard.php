<?php
$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';
session_start();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Sign </title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>

<body>
    <header>
        <?php require_once "header1.php"; ?>
    </header>

    <main id="leaderboard-main">
        <div class="dbleaderboard-gradient-wrapper">
            <div class="dbleaderboard-container">
                <?php
// Définition de la requête SQL pour le classement
$sql = "SELECT 
            u.username, 
            COUNT(s.id) AS levels_completed, 
            SUM(s.moves) AS total_moves, 
            MIN(s.date) AS first_record_date
        FROM users u
        JOIN stats s ON s.user_id = u.id
        GROUP BY u.id
        ORDER BY levels_completed DESC, total_moves ASC, first_record_date ASC";
$stmt = $pdo->query($sql);
//nitialisation du compteur de classement
$rank = 1;
?>

<table class="dbleaderboard">
    <thead>
        <tr>
            <th>Ranking</th>
            <th>Username</th>
            <th>Total moves</th>
            <th>Levels completed</th>
        </tr>
    </thead>
    <tbody>
            
<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : 
    $isUser = isset($_SESSION['username']) && $row['username'] === $_SESSION['username'];
?>
    <tr<?= $isUser ? ' class="my-score"' : '' ?>>
        <td><?= $rank++ ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= $row['total_moves'] ?></td>
        <td><?= $row['levels_completed'] ?></td>
    </tr>
<?php endwhile; ?>
    </tbody>
</table>
            </div>
        </div>
    </main>

</body>
</html>
