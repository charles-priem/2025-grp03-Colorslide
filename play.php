<?php

if (session_status() === PHP_SESSION_NONE) session_start();


$host = 'localhost';
$dbname = 'bddgrp03colorslide';
$username = 'root';
$password = 'root';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $pdo = null; // Ne bloque pas la page si la base est KO
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Play</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css" integrity="sha384-NvKbDTEnL+A8F/AA5Tc5kmMLSJHUO868P+lDtTpJIeQdGYaUIuLr4lVGOEA1OcMy" crossorigin="anonymous">
        <?php
        $niveau = $_GET["level"] ?? 1;
        $prec = $niveau < 2 ? 1 : $niveau - 1;
        $suiv = $niveau + 1;
        // echo "<div><a href='play2.php?level=$prec'>Previous level</a> - <a href='play2.php?level=$niveau'>Current level</a> - <a href='play2.php?level=$suiv'>Next level</a></div>";
    ?>
    <style>

        .hint-dropdown {
            position: absolute;
            top: 44px;
            right:50%;
            transform: translateX(50%);
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 8px 0;
            width: 127px;
            z-index: 100;
            display: none;
            background-color:#0A1539
        }

      .hint-dropdown::after{
        content: '';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #0A1539; /* Couleur du fond du dropdown */
        width: 0;
        height: 0;
        z-index: 100;
      }

        .hint-dropdown::before{
        content: '';
        position: absolute;
        top: -11px; /* 1px plus haut pour la bordure */
        left: 50%;
        transform: translateX(-50%);
        border-left: 11px solid transparent;
        border-right: 11px solid transparent;
        border-bottom: 11px solid #ccc; /* Même couleur que la bordure du dropdown */
        width: 0;
        height: 0;
        z-index: 99;
        }
        .hint-dropdown button {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            width:100%;
            font-size: 1rem;
            border-radius: 0px;

            
            /* background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%); */
            color: #fff;
            border: none;
            font-family: 'Space Grotesk', Arial, sans-serif;
            cursor: pointer;
            z-index: 1;
            overflow: hidden;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            letter-spacing: 0.5px;
            background-color: #0A1539
        }
        .hint-dropdown button:hover {
            background:rgb(0, 16, 49);
        }
 
        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .game-container {
            background-color:white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            grid-template-rows: repeat(10, 1fr);
        }

        .cell {
            background-color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-user-select: none;
            user-select: none;
            position: relative;
            overflow: hidden;
        }

        .cell.wall {
            background-color: #333333;
        }

        .player {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 9;
            transition: transform 0.3s none;
            background-image: url("game/sprites/sprite.png");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .player.animating {
            position: fixed;
            z-index: 9;
        }

        .player.falling {
            animation: fallAnimation 0.5s forwards;
        }

        @keyframes fallAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0);
                opacity: 0;
            }
        }

        .fill {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scale(0);
            transform-origin: center;
            z-index: 1;
        }

        /* Skins de traînée */

        .cell.visited .fill { 
            background-color: pink;
            transform: scale(1);
            display: flex;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            justify-content: space-between;
        }

        .controls .logo{
            position:absolute;
            width:100px !important;
            height:100px !important;
            top:0;
            left:50%;
            transform: translateX(-50%);
            margin-top:-22px;
        }

        button {
            font-size: 16px;
            background-color:white; 
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .instructions {
            margin-top: 20px;
            max-width: 400px;
            text-align: center;
            color: #555;
        }
        
        .tp {
            background-image: url("game/sprites/TP.gif");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .hole {
            background-color: red;
        }

        /* Popup de victoire stylé RGB animé */
        .popup {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            /* Modifiez pas le z-index pitié */
            z-index: 1099; 
            animation: popup-fade-in 0.3s;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .popup.show {
            opacity: 1;
        }
        @keyframes popup-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .popup-content {
            position: relative;
            min-width: 320px;
            max-width: 95vw;
            padding:32px;
            border-radius: 18px;
            background: linear-gradient(135deg, #1c1f2b 0%, #232946 100%);
            color: #fff;
            text-align: center;
            box-shadow: 0 8px 32px rgba(30,41,59,0.25), 0 1.5px 6px #6366f1;
            border: 4px solid transparent;
            background-clip: padding-box, border-box;
            background-origin: padding-box, border-box;
            background-image:
                linear-gradient(135deg, #1c1f2b 0%, #232946 100%),
                conic-gradient(from var(--angle, 0deg), #FF6D1B, #FFEE55, #5BFF89, #4D8AFF, #6B5FFF, #FF64F9, #FF6565, #FF6D1B);
            animation: spin 3s linear infinite, popup-content-bounce 0.4s;
        }
        .popup-content::before {
            content: '';
            position: absolute;
            inset: -18px;
            border-radius: 22px;
            z-index: -1;
            background: conic-gradient(from var(--angle, 0deg), #FF6D1B, #FFEE55, #5BFF89, #4D8AFF, #6B5FFF, #FF64F9, #FF6565, #FF6D1B);
            filter: blur(1.5rem);
            opacity: 0;
            transition: opacity 0.25s; /* Retire le délai */
            pointer-events: none;
            animation: spin 3s linear infinite;
        }
        .popup.show .popup-content::before {
            opacity: 0.45;
        }
        .popup-content h2 {
            color: white;
            font-size: 2.1rem;
            letter-spacing: 1px;
        }
        .popup-content p {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .popup-content button {
            position: relative;
            background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 48px;
            font-size: 1.2rem;
            font-family: 'Space Grotesk', Arial, sans-serif;
            font-weight: 700;
            cursor: pointer;
            z-index: 1;
            overflow: hidden;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            margin-top: 12px;
            letter-spacing: 0.5px;
        }       

        .popup-content button:hover {
            /* background: linear-gradient(90deg, #06b6d4 0%, #6366f1 100%); */
            /* box-shadow: 0 0 32px 4px rgba(99,102,241,0.22); */
            transform: scale(1.04);
        }
        .close {
            position: absolute;
            right: 18px;
            top: 14px;
            font-size: 28px;
            color: #64748b;
            cursor: pointer;
            transition: color 0.1s, transform 0.1s;
            z-index: 10;
        }
        .close:hover {
            color: #FF6D1B;
            transform: scale(1.2);
        }
        .hint-highlight {
            animation: hint-pulse 1s infinite;
            box-shadow: inset 0 0 10px 2px rgba(255, 215, 0, 0.7);
        }

        @keyframes hint-pulse {
            0% { box-shadow: inset 0 0 10px 2px rgba(255, 215, 0, 0.3); }
            50% { box-shadow: inset 0 0 15px 4px rgba(255, 215, 0, 0.9); }
            100% { box-shadow: inset 0 0 10px 2px rgba(255, 215, 0, 0.3); }
        }
    </style>
</head>
<body>
    <div class="menu-icon" onclick="toggleDropdown()">
    🏆
    </div>
    <header>
        <?php include 'php/header1.php'; ?> 
    </header>
    <main id="play">
        <div class="game-wrapper">
            <div class="game-container">
                <div class="controls">
                    <div class="left-wrapper">
                    <a>
                        <img src="icons/logo_home_blue.png" alt="Home">
                    </a>
                    <button id="new-game"><img src="icons/recharger.png" alt="Restart"></button>
                    </div>
                    <a href="/2025-grp03/index.php"><img class="logo" src="/2025-grp03/images/Logo.png" classe="logo"></a>


                    <div class="right-wrapper">
                    <form method="POST" action="game/scripts/solveur.php" id="hint-form">
                        <!-- <input type="hidden" name="level_name" value="<//?= $_GET["level"] ?? 1 ?>"> -->
                        <input type="hidden" name="current_state" id="currentState">
                        <input type="hidden" name="statesaved" id="statesaved">
                        <input type="hidden" name="mode" id="hint-mode" value="">
                        <button type="button" class="hint" id="hint-btn">
                            <img src="icons/point-dinterrogation.png" alt="Hint">
                        </button>
                        <div class="hint-dropdown" id="hint-dropdown" style="display:none;">
                        <button type="button" id="run-solution">Solution</button>
                        <button type="button" id="hint-only-btn">Hint</button>
                    </div>
                    </form>

                    <a>
                        <img class="parameters" src="icons/parametres.png" alt="Parameters">
                    </a>
                    </div>

                </div>
                <div id="grid" class="grid"></div>


                <?php
                    if (isset($_SESSION["solution"])): ?>
                        <script>
                            sessionStorage.setItem("solution", "<?= htmlspecialchars($_SESSION["solution"]) ?>");
                        </script>
                    <?php 
                    unset($_SESSION["solution"]);
                    endif;
                    ?>
            </div>
            <div class="dropdown" id="leaderboard">
                <table><!--/td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Dave</td>
                            <td>90</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Eve</td>
                            <td>85</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Natys</td>
                            <td>85</td>
                        </tr>

                    </tbody>-->
                    <thead>
            <tr><th>#</th><th>Nom</th><th>Score</th></tr>
        </thead>
        <tbody>
            
        <?php
            $level = $_GET['level'] ?? 1;
                    //modif bd
            if ($pdo) {
                $stmt = $pdo->prepare("
                    SELECT pseudo, mouvements                   
                    FROM leaderboard
                    WHERE level = ?
                    ORDER BY mouvements ASC, date_enregistrement ASC
                    LIMIT 10
                ");
                $stmt->execute([$level]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $rank = 1;
                foreach ($results as $row) {
                    $isCurrentUser = isset($_SESSION['username']) && $_SESSION['username'] === $row['pseudo'];
                    echo "<tr" . ($isCurrentUser ? " class='highlight'" : "") . ">";
                    echo "<td>{$rank}</td>";
                    echo "<td>{$row['pseudo']}</td>";
                    echo "<td>{$row['mouvements']}</td>";
                    echo "</tr>";
                    $rank++;
                }
            } else {
                echo "<tr><td colspan='3'>Base de données indisponible</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
            
        
</div>


<!-- Popup de victoire -->
<div id="win-popup" class="popup" style="display:none;">
    <div class="popup-content">
        <span id="close-popup" class="close">&times;</span>
        <h2>Congratulations !</h2>
        <p id="win-message"></p>
        <button onclick="closeWinPopup()">Fermer</button>
    </div>
</div>

        
<script>


function toggleDropdown() {
    const menuIcon = document.querySelector('.menu-icon');
    const dropdown = document.getElementById('leaderboard');

    // Ajoute ou supprime les classes "open" pour déclencher les transitions
    if (dropdown.classList.contains('open')) {
        dropdown.classList.remove('open'); // Ferme le menu
        menuIcon.classList.remove('open'); // Ramène l'icône à sa position initiale
    } else {
        dropdown.classList.add('open'); 
        menuIcon.classList.add('open'); // Déplace l'icône vers la gauche

    }
}

// Ferme le menu si on clique en dehors
window.addEventListener('click', function (e) {
    const menuIcon = document.querySelector('.menu-icon');
    const dropdown = document.getElementById('leaderboard');

    if (!menuIcon.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('open'); // Ferme le menu
        menuIcon.classList.remove('open'); // Ramène l'icône à sa position initiale
    }
});



// Script du jeu 

        const EMPTY = -1;
        const VISITED = 0;
        const PATH = 1;
        const WALL = 2;
        const TP = 3;
        const HOLE = 4;
        const PLAYER = 5;

        let rows, cols, playerPos, playground;
        let originalPlayerPos, originalPlayground;
        let hintTimeout = null;
        let highlightedCells = [];

        <?php
            $savedState = null;
            if (isset($_SESSION['current_state'])) {
                $savedState = json_decode($_SESSION['current_state'], true);
                unset($_SESSION['current_state']); // Nettoyer après utilisation
            }
            $savedplayerPos=null;
            if (isset($_SESSION['savedplayerPos'])) {
                $savedplayerPos = json_decode($_SESSION['savedplayerPos'], true);
                unset($_SESSION['savedplayerPos']); // Nettoyer après utilisation
            }
            $mode=null;
            if (isset($_SESSION['mode'])) {
                $mode = json_decode($_SESSION['mode'], true);
                unset($_SESSION['mode']); // Nettoyer après utilisation
            }
        ?>

        const savedState = <?= $savedState ? json_encode($savedState) : 'null' ?>;
        const savedplayerPos = <?= $savedplayerPos ? json_encode($savedplayerPos) : 'null' ?>;
        const mode = <?= $mode ? json_encode($mode) : 'null' ?>;
        
        async function loadLevel(level) {
            try {
                const response = await fetch(`game/levels/${level}.json`);
                if (!response.ok) throw new Error("Error while loading level");
                const data = await response.json();
                return decodeJSON(data);
            } catch (error) {
                console.error("Error:", error);
                return null;
            }
        }

        function decodeJSON(data) {
            const rows = data[0];
            const cols = data[1];
            const playground = data.slice(2);

            const playerIndex = playground.indexOf(PLAYER);
            const playerPos = {row: Math.floor(playerIndex / cols), col: playerIndex % cols};
            
            const playgroundGrid = [];
            for (let i = 0; i < playground.length; i += cols) {
                playgroundGrid.push(playground.slice(i, i + cols));
            }

            return {
                rows: rows,
                cols: cols,
                playerPos: playerPos,
                playground: playgroundGrid
            };
        }

        function showWinPopup(moves) {
            const popup = document.getElementById('win-popup');
            document.getElementById('win-message').textContent = `You completed the level in ${moves} moves !`;
            popup.style.display = 'flex';
            setTimeout(() => popup.classList.add('show'), 10);
            gameEnded = true; // Empêche tout déplacement après victoire
        }

        function closeWinPopup() {
            const popup = document.getElementById('win-popup');
            popup.classList.remove('show');
            setTimeout(() => popup.style.display = 'none', 300);
        }

        function showTip() {
            const solution = sessionStorage.getItem("solution");
            if (!solution || solution.length === 0 || solution === "No path found.") return;

            // Annuler le clignotement précédent s'il existe
            clearHint();

            // Trouver la première direction de la solution
            const firstMove = solution[0];
            highlightedCells = [];

            // Déterminer les cellules à mettre en surbrillance selon le premier mouvement
            switch (firstMove) {
                case 'N': // Nord - toute la colonne vers le haut jusqu'au premier mur
                    for (let row = playerPos.row - 1; row >= 0; row--) {
                        if (playground[row][playerPos.col] === WALL || playground[row][playerPos.col] === HOLE) break;
                        highlightedCells.push({row: row, col: playerPos.col});
                    }
                    break;
                case 'S': // Sud - toute la colonne vers le bas jusqu'au premier mur
                    for (let row = playerPos.row + 1; row < rows; row++) {
                        if (playground[row][playerPos.col] === WALL || playground[row][playerPos.col] === HOLE) break;
                        highlightedCells.push({row: row, col: playerPos.col});
                    }
                    break;
                case 'E': // Est - toute la ligne vers la droite jusqu'au premier mur
                    for (let col = playerPos.col + 1; col < cols; col++) {
                        if (playground[playerPos.row][col] === WALL || playground[playerPos.row][col] === HOLE) break;
                        highlightedCells.push({row: playerPos.row, col: col});
                    }
                    break;
                case 'O': // Ouest - toute la ligne vers la gauche jusqu'au premier mur
                    for (let col = playerPos.col - 1; col >= 0; col--) {
                        if (playground[playerPos.row][col] === WALL || playground[playerPos.row][col] === HOLE) break;
                        highlightedCells.push({row: playerPos.row, col: col});
                    }
                    break;
            }

            // Filtrer seulement les cellules valides
            highlightedCells = highlightedCells.filter(pos => 
                pos.row >= 0 && pos.row < rows && 
                pos.col >= 0 && pos.col < cols
            );

            // Ajouter la classe de clignotement
            highlightedCells.forEach(pos => {
                const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
                if (cell) {
                    cell.classList.add('hint');
                }
            });
            
            // Configurer le timeout pour retirer le clignotement
            hintTimeout = setTimeout(clearHint, 3000);
        }

        // Fonction pour effacer le clignotement
        function clearHint() {
            if (hintTimeout) {
                clearTimeout(hintTimeout);
                hintTimeout = null;
            }
            
            highlightedCells.forEach(pos => {
                const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
                if (cell) {
                    cell.classList.remove('hint');
                }
            });
            
            highlightedCells = [];
        }

        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.getElementById('grid');
            const newGameButton = document.getElementById('new-game');         
            const urlParams = new URLSearchParams(window.location.search);
            const levelName = urlParams.get('level') || "1";   
            
            let isMoving = false;
            let animationFrameId = null;
            let TPs = [];

            if (savedState) {
                // Décoder l'état sauvegardé comme un niveau normal
                const savedLevelData = decodeJSON(savedState);
                if (savedplayerPos){
                    savedLevelData.playerPos= {row:savedplayerPos[0], col:savedplayerPos[1]};
                }

                // Mettre à jour les références originales
                rows = savedLevelData.rows;
                cols = savedLevelData.cols;

                originalPlayerPos = {...savedLevelData.playerPos}; 
                originalPlayground = JSON.parse(JSON.stringify(savedLevelData.playground));
                
                // Initialiser avec l'état sauvegardé
                playerPos = {...originalPlayerPos};
                playground = JSON.parse(JSON.stringify(originalPlayground));
                
                initGame();
            }
            else{
                loadLevel(levelName).then(levelData => {
                    if (levelData) {
                        rows = levelData.rows;
                        cols = levelData.cols;
                        
                        // Sauvegarde de l'état original
                        originalPlayerPos = {...levelData.playerPos};
                        originalPlayground = JSON.parse(JSON.stringify(levelData.playground));
                        
                        // Initialisation avec les valeurs originales
                        playerPos = {...originalPlayerPos};
                        playground = JSON.parse(JSON.stringify(originalPlayground));
                        
                        initGame();
                    } else {
                        console.error("Level not found.");
                        alert("Level not found.");
                    }
                }).catch(error => {
                    console.error("Error while loading:", error);
                });
            }

            document.querySelector('form').addEventListener('submit', function(e) {
                // Clone profond du tableau 2D
                const clonedPlayground = JSON.parse(JSON.stringify(playground));
                
                // Mettre à jour la position du joueur dans le clone
                clonedPlayground[originalPlayerPos.row][originalPlayerPos.col] = VISITED;

                // Aplatir le tableau 2D en 1D et ajouter rows/cols devant
                const flatPlayground = clonedPlayground.flat();
                const stateToSend = [rows, cols, ...flatPlayground];

                document.getElementById('statesaved').value = JSON.stringify([playerPos.row, playerPos.col]);
                
                document.getElementById('currentState').value = JSON.stringify(stateToSend);
            });


            function initGame() {
                // Réinitialisation à l'état original
                // playerPos = {...originalPlayerPos};
                // playground = JSON.parse(JSON.stringify(originalPlayground));
                

                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                }

                grid.innerHTML = '';
                TPs = [];

                grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
                grid.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        if (playground[row][col] === TP) {
                            TPs.push({ row, col });
                        }
                    }
                }

                isMoving = false;

                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        const cell = document.createElement('div');
                        cell.className = 'cell';
                        cell.dataset.row = row;
                        cell.dataset.col = col;

                        if (playground[row][col] === WALL) {
                            cell.classList.add('wall');
                        } else if (playground[row][col] === TP) {
                            cell.classList.add('tp');
                        } else if (playground[row][col] === HOLE) {
                            cell.classList.add('hole');
                        }

                        const fill = document.createElement('div');
                        fill.className = 'fill';
                        cell.appendChild(fill);

                        if (playground[row][col] === VISITED || (playground[row][col]===PATH && (col === playerPos.col && row === playerPos.row)) || playground[row][col]===PLAYER) {
                            cell.classList.add('visited');
                        }

                        if (row === playerPos.row && col === playerPos.col) {
                            const player = document.createElement('div');
                            player.className = 'player';
                            cell.appendChild(player);
                        }

                        grid.appendChild(cell);
                    }
                }
            }

            function animateMovement(startPos, endPos, direction, callback) {
                const playerElement = document.querySelector('.player');
                if (!playerElement) return;

                const startCell = grid.children[startPos.row * cols + startPos.col];
                const endCell = grid.children[endPos.row * cols + endPos.col];

                const gridRect = grid.getBoundingClientRect();
                const startCellRect = startCell.getBoundingClientRect();
                const endCellRect = endCell.getBoundingClientRect();

                const startX = startCellRect.left - gridRect.left + startCellRect.width / 2;
                const startY = startCellRect.top - gridRect.top + startCellRect.height / 2;
                const endX = endCellRect.left - gridRect.left + endCellRect.width / 2;
                const endY = endCellRect.top - gridRect.top + endCellRect.height / 2;

                const distanceX = endX - startX;
                const distanceY = endY - startY;

                playerElement.classList.add('animating');
                playerElement.style.position = 'fixed';
                playerElement.style.left = startCellRect.left + 'px';
                playerElement.style.top = startCellRect.top + 'px';
                playerElement.style.width = startCellRect.width + 'px';
                playerElement.style.height = startCellRect.height + 'px';
                playerElement.style.transform = 'translate(0, 0)';

                const startTime = performance.now();
                const pixelsPerMillisecond = 1;
                const totalDistance = Math.sqrt(distanceX ** 2 + distanceY ** 2);
                const duration = totalDistance / pixelsPerMillisecond;

                function step(timestamp) {
                    const elapsed = timestamp - startTime;
                    const distanceParcourue = elapsed * pixelsPerMillisecond;
                    const progress = Math.min(distanceParcourue / totalDistance, 1);

                    let translateX = distanceX * progress;
                    let translateY = distanceY * progress;

                    playerElement.style.transform = `translate(${translateX}px, ${translateY}px)`;

                    markVisitedCells(startPos, endPos, direction, progress);

                    if (progress < 1) {
                        animationFrameId = requestAnimationFrame(step);
                    } else {
                        playerElement.style.transform = '';
                        playerElement.classList.remove('animating');
                        playerElement.style.position = '';
                        playerElement.style.left = '';
                        playerElement.style.top = '';
                        playerElement.style.width = '';
                        playerElement.style.height = '';

                        if (startCell.contains(playerElement)) {
                            startCell.removeChild(playerElement);
                        }

                        endCell.appendChild(playerElement);
                        callback();
                    }
                }

                animationFrameId = requestAnimationFrame(step);
            }

            function markVisitedCells(startPos, endPos, direction, progress) {
                if (playground[endPos.row][endPos.col] === TP) {
                    const totalSteps = Math.max(
                        Math.abs(endPos.row - startPos.row),
                        Math.abs(endPos.col - startPos.col)
                    ) - 1;
                    
                    const currentStep = Math.min(Math.floor(progress * totalSteps), totalSteps);
                    
                    for (let step = 0; step <= currentStep; step++) {
                        let row, col;

                        switch (direction) {
                            case 'up':
                                row = startPos.row - step;
                                col = startPos.col;
                                break;
                            case 'down':
                                row = startPos.row + step;
                                col = startPos.col;
                                break;
                            case 'left':
                                row = startPos.row;
                                col = startPos.col - step;
                                break;
                            case 'right':
                                row = startPos.row;
                                col = startPos.col + step;
                                break;
                        }

                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;
                        
                        if (playground[row][col] === TP) break;

                        if (playground[row][col] === PATH) {
                            playground[row][col] = VISITED;
                            const cell = grid.children[row * cols + col];
                            cell.classList.add('visited');
                        }
                    }
                } else {
                    const totalSteps = Math.max(
                        Math.abs(endPos.row - startPos.row),
                        Math.abs(endPos.col - startPos.col)
                    );
                    
                    const currentStep = Math.floor(progress * totalSteps);
                    
                    for (let step = 0; step <= currentStep; step++) {
                        let row, col;

                        switch (direction) {
                            case 'up':
                                row = startPos.row - step;
                                col = startPos.col;
                                break;
                            case 'down':
                                row = startPos.row + step;
                                col = startPos.col;
                                break;
                            case 'left':
                                row = startPos.row;
                                col = startPos.col - step;
                                break;
                            case 'right':
                                row = startPos.row;
                                col = startPos.col + step;
                                break;
                        }

                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;

                        if (playground[row][col] === PATH) {
                            playground[row][col] = VISITED;
                            const cell = grid.children[row * cols + col];
                            cell.classList.add('visited');
                        }
                    }
                }
            }

            function fallIntoHole(callback) {
                const playerElement = document.querySelector('.player');
                if (!playerElement) return;

                playerElement.classList.add('falling');
                
                setTimeout(() => {
                    if (playerElement.parentNode) {
                        playerElement.parentNode.removeChild(playerElement);
                    }
                    callback();
                }, 500);
            }
            
            function slide(direction) {
                clearHint(); 
                if (isMoving) return;
                isMoving = true;
                let fallen = false;
                let newRow = playerPos.row;
                let newCol = playerPos.col;
                let moved = false;
                const startPos = { ...playerPos };
                let teleportDestination = null;
                
                while (true) {
                    let nextRow = newRow;
                    let nextCol = newCol;

                    switch (direction) {
                        case 'up': nextRow--; break;
                        case 'down': nextRow++; break;
                        case 'left': nextCol--; break;
                        case 'right': nextCol++; break;
                    }

                    if (nextRow < 0 || nextRow >= rows || nextCol < 0 || nextCol >= cols || 
                        playground[nextRow][nextCol] === WALL) {
                        break;
                    }

                    if (playground[nextRow][nextCol] === TP) {
                        const otherTP = TPs.find(tp => !(tp.row === nextRow && tp.col === nextCol));
                        
                        if (otherTP) {
                            teleportDestination = { 
                                source: { row: nextRow, col: nextCol },
                                destination: otherTP
                            };
                            
                            newRow = nextRow;
                            newCol = nextCol;
                            moved = true;
                            break;
                        }
                    }

                    if (playground[nextRow][nextCol] === HOLE) {
                        fallen = true;
                        newRow = nextRow;
                        newCol = nextCol;
                        moved = true;
                        break;
                    }

                    newRow = nextRow;
                    newCol = nextCol;
                    moved = true;
                }

                if (moved) {
                    animateMovement(startPos, { row: newRow, col: newCol }, direction, () => {
                        if (teleportDestination) {
                            const tpDest = teleportDestination.destination;
                            
                            const oldCell = grid.children[newRow * cols + newCol];
                            const newCell = grid.children[tpDest.row * cols + tpDest.col];
                            const player = document.querySelector('.player');
                            
                            if (player && oldCell.contains(player)) {
                                oldCell.removeChild(player);
                                newCell.appendChild(player);
                            }
                            
                            playerPos.row = tpDest.row;
                            playerPos.col = tpDest.col;
                            
                            let afterTPRow = tpDest.row;
                            let afterTPCol = tpDest.col;
                            
                            switch (direction) {
                                case 'up': afterTPRow--; break;
                                case 'down': afterTPRow++; break;
                                case 'left': afterTPCol--; break;
                                case 'right': afterTPCol++; break;
                            }
                            
                            isMoving = false;
                            
                            if (afterTPRow >= 0 && afterTPRow < rows && 
                                afterTPCol >= 0 && afterTPCol < cols && 
                                playground[afterTPRow][afterTPCol] !== WALL) {
                                slide(direction);
                            } else {
                                checkWin();
                            }
                        } 
                        else if (fallen) {
                            fallIntoHole(() => {
                                isMoving = false;
                                sessionStorage.setItem("moves", "0");
                                const niveau = new URLSearchParams(window.location.search).get("level") ?? 1;
                                document.location.href = `play2.php?level=${niveau}`;
                            });
                        } 
                        else {
                            playerPos.row = newRow;
                            playerPos.col = newCol;
                            isMoving = false;
                            checkWin();
                            let moves = parseInt(sessionStorage.getItem("moves")) || 0;
                            moves++;
                            sessionStorage.setItem("moves", moves.toString());
                        }
                    });
                } else {
                    isMoving = false;
                }
            }
            
            function isDropdownMenuOpen() {
                const menuToggle = document.getElementById('menu_toggle');
                return menuToggle && menuToggle.checked;
            }

            function checkWin() {
                let NotVisited = playground.some(l => l.some(n => n === PATH));
                if (!NotVisited) setTimeout(() => {
                    alert(`Félicitations ! Vous avez rempli toute la grille en  ${sessionStorage.getItem("moves")} mouvements !`);
                }, 50);
            }
            
            newGameButton.addEventListener('click', () => {
                sessionStorage.setItem("moves", "0");
                const niveau = new URLSearchParams(window.location.search).get("level") ?? 1;
                    document.location.href = `play.php?level=${niveau}`;
            });

            if ((performance.navigation.type == performance.navigation.TYPE_RELOAD || performance.navigation.type == performance.navigation.TYPE_NAVIGATE) && !savedState) {
                sessionStorage.setItem("moves", "0");
            }

            document.addEventListener('keydown', (e) => {
                if (isDropdownMenuOpen()) return; // <-- Ajout : bloque le jeu si menu ouvert
                switch (e.key) {
                    case 'ArrowUp': slide('up'); break;
                    case 'ArrowDown': slide('down'); break;
                    case 'ArrowLeft': slide('left'); break;
                    case 'ArrowRight': slide('right'); break;
                }
            });

            // Fonction pour exécuter la solution automatiquement
            async function executeSolution(solution) {
                for (const char of solution) {
                    switch (char) {
                        case 'N': await executeMove('up'); break;
                        case 'E': await executeMove('right'); break;
                        case 'S': await executeMove('down'); break;
                        case 'O': await executeMove('left'); break;
                    }
                    await sleep(200); // Attendre un peu entre chaque mouvement
                }
            }

            function executeMove(direction) {
                return new Promise(resolve => {
                    slide(direction);
                    
                    // Vérifier régulièrement si le mouvement est terminé
                    const checkInterval = setInterval(() => {
                        if (!isMoving) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 50);
                });
            }

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            // Configurer le bouton pour exécuter la solution
            const runSolutionButton = document.getElementById('run-solution')
            console.log(runSolutionButton);
            if (runSolutionButton) {
                runSolutionButton.addEventListener('click', () => {
                    const solution = sessionStorage.getItem("solution");
                    let NotVisited = playground.some(l => l.some(n => n === PATH));
                    if (solution && NotVisited) {
                        executeSolution(solution);
                    }
                });
            }

            // Configurer le bouton pour afficher un conseil
            const tipsButton = document.getElementById('tips-giving');
            if (tipsButton) {
                tipsButton.addEventListener('click', showTip);
            }

            const hintBtn = document.getElementById('hint-btn');
            const hintDropdown = document.getElementById('hint-dropdown');

            hintBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                hintDropdown.style.display = hintDropdown.style.display === "block" ? "none" : "block";
            });

            document.addEventListener('click', (e) => {
                if (hintDropdown.style.display === "block" && !hintDropdown.contains(e.target) && e.target !== hintBtn) {
                    hintDropdown.style.display = "none";
                }
            });
            });

            // Gestion du formulaire et des boutons
            document.getElementById('hint-only-btn').addEventListener('click', function() {
                prepareHintForm('hint');
                document.getElementById('hint-form').submit();
            });

            document.getElementById('run-solution').addEventListener('click', function() {
                prepareHintForm('solution');
                document.getElementById('hint-form').submit();
            });

            function prepareHintForm(mode) {
                // Clone profond du tableau 2D
                const clonedPlayground = JSON.parse(JSON.stringify(playground));
                
                // Mettre à jour la position du joueur dans le clone
                clonedPlayground[originalPlayerPos.row][originalPlayerPos.col] = VISITED;

                // Aplatir le tableau 2D en 1D et ajouter rows/cols devant
                const flatPlayground = clonedPlayground.flat();
                const stateToSend = [rows, cols, ...flatPlayground];

                document.getElementById('statesaved').value = JSON.stringify([playerPos.row, playerPos.col]);
                document.getElementById('currentState').value = JSON.stringify(stateToSend);
                document.getElementById('hint-mode').value = mode;
            }

            // Fonction pour afficher un indice visuel
            function showVisualHint() {
                const solution = sessionStorage.getItem("solution");
                if (!solution || solution === "No path found.") return;

                clearHint();

                // Trouver le prochain mouvement possible
                const nextMove = solution[0];
                const directions = {
                    'N': { row: -1, col: 0 },
                    'S': { row: 1, col: 0 },
                    'E': { row: 0, col: 1 },
                    'O': { row: 0, col: -1 }
                };

                const direction = directions[nextMove];
                let currentRow = playerPos.row;
                let currentCol = playerPos.col;

                // Marquer les cellules dans la direction du mouvement
                while (true) {
                    currentRow += direction.row;
                    currentCol += direction.col;

                    if (currentRow < 0 || currentRow >= rows || 
                        currentCol < 0 || currentCol >= cols || 
                        playground[currentRow][currentCol] === WALL || 
                        playground[currentRow][currentCol] === HOLE) {
                        break;
                    }

                    highlightedCells.push({ row: currentRow, col: currentCol });
                    const cell = document.querySelector(`.cell[data-row="${currentRow}"][data-col="${currentCol}"]`);
                    if (cell) {
                        cell.classList.add('hint-highlight');
                    }
                }

                // Configurer le timeout pour retirer le surlignage
                hintTimeout = setTimeout(clearHint, 3000);
            }

            // Fonction pour effacer l'indice visuel
            function clearHint() {
                if (hintTimeout) {
                    clearTimeout(hintTimeout);
                    hintTimeout = null;
                }
                
                highlightedCells.forEach(pos => {
                    const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
                    if (cell) {
                        cell.classList.remove('hint-highlight');
                    }
                });
                
                highlightedCells = [];
            }

</script>
    </main>
    <footer>
        <?php include 'php/footer.php'; ?>
    </footer>
</body>
</html>