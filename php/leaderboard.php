<?php
$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';
session_start();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //évite les erreurs silencieuses
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Leaderboard</title>
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
                // Requête SQL pour récupérer tout le leaderboard
                $sql = "SELECT 
                            u.id,
                            u.username, /*On récupère l’id et le username des utilisateurs depuis la table users (alias u).*/
                            COUNT(s.id) AS levels_completed, 
                            SUM(s.moves) AS total_moves, /*On additionne tous les coups joués par chaque utilisateur*/
                            MIN(s.date) AS first_record_date
                        FROM users u
                        JOIN stats s ON s.user_id = u.id
                        GROUP BY u.id
                        ORDER BY levels_completed DESC, total_moves ASC, first_record_date ASC";
                $stmt = $pdo->query($sql);

                // On stocke tout le leaderboard dans un tableau pour pouvoir retrouver le rang de l'utilisateur connecté
                $leaderboard = [];
                $myRank = null;
                $myRow = null;
                $rank = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $leaderboard[] = $row;
                    if (
                        isset($_SESSION['user_id']) &&
                        $row['id'] == $_SESSION['user_id']
                    ) {
                        $myRank = $rank;
                        $myRow = $row;
                    }
                    $rank++;
                }
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
                    <?php if ($myRow): ?>
                        <tr class="my-score">
                            <td><?= $myRank ?></td>
                            <td><strong>Moi</strong></td>
                            <td><?= $myRow['total_moves'] ?></td>
                            <td><?= $myRow['levels_completed'] ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    $rank = 1;
                    foreach ($leaderboard as $row):
                        $isUser = isset($_SESSION['user_id']) && $row['id'] == $_SESSION['user_id'];
                    ?>
                        <tr<?= $isUser ? ' class="my-score"' : '' ?>>
                            <td><?= $rank ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= $row['total_moves'] ?></td>
                            <td><?= $row['levels_completed'] ?></td>
                        </tr>
                    <?php
                        $rank++;
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer>
        <?php require_once "footer.php"; ?>
    </footer>

</body>
</html>
