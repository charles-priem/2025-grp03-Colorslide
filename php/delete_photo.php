<?php
session_start();
if (isset($_POST['user_id']) && $_POST['user_id'] == $_SESSION['user_id']) {
    $pdo = new PDO('mysql:host=localhost;dbname=bddgrp03colorslide;charset=utf8', 'root', 'root');
    $stmt = $pdo->prepare("UPDATE users SET avatar = '' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
header("Location: dashboard.php");
exit();
?>