<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        session_start();

        // $levelFile = "../levels/" . ($_POST["level_name"] ?? 1) . ".json";
        // $_SESSION["levelPath"] = $levelFile;
        // //exec("solveur.exe " . escapeshellarg($levelFile));

        // $jsonString = file_get_contents($levelFile);
        // $data = json_decode($jsonString, true);
        $_SESSION['current_state'] = $_POST['current_state'];
        $data = json_decode($_POST["current_state"], true);
        if (json_last_error() !== JSON_ERROR_NONE) die('Erreur de décodage JSON : ' . json_last_error_msg());
        
        $rows = $data[0];
        $cols = $data[1];
        $playground = array_slice($data, 2, $rows * $cols);
        $playgroundStr = implode(',', $playground);

        exec("solveur.exe " . escapeshellarg($rows) . " " . escapeshellarg($cols) . " " . escapeshellarg($playgroundStr), $output, $return_var);

        $solution = file_get_contents("solution.txt"); // Possibilité de juste utiliser $output[0] et donc ne pas passer par fichier
        $_SESSION["solution"] = $solution ? $solution : "Aucune solution trouvée."; // erreur si return_var != 0
    }

    header("location: " . $_SERVER["HTTP_REFERER"]);