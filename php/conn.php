<?php
    $dbname = "grp03-db";

    $conn = new PDO("mysql:host=localhost;dbname=$dbname", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);