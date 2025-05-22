<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

try {
    $data = json_decode($_POST["current_state"], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON in current_state');
    }
    
    $playerPos = json_decode($_POST["statesaved"], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON in statesaved');
    }

    $rows = $data[0];
    $cols = $data[1];
    $playground = array_slice($data, 2, $rows * $cols);
    
    // Place the player in the playground
    $playground[$playerPos[0] * $cols + $playerPos[1]] = 5;
    
    // Prepare solver input
    $playgroundStr = implode(",", $playground);
    $params = escapeshellarg($rows) . " " . escapeshellarg($cols) . " " . escapeshellarg($playgroundStr);
    
    // Run solver
    exec("solveur.exe " . $params, $output, $return_var);
    
    // Return solution
    $solution = $return_var === 0 ? implode("", $output) : null;
    echo json_encode([
        "success" => $return_var === 0,
        "solution" => $solution,
        "error" => $return_var !== 0 ? "Solver execution failed" : null
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}