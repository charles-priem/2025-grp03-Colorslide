<?php


$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=bddgrp03colorslide;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}