

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
 
        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .game-container {
            position: relative;
            background-color:white;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            box-sizing: border-box;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            grid-template-rows: repeat(10, 1fr);
            width: 100%;
            height: 100%;
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
            z-index: 10;
            transition: transform 0.3s none;
            background-image: url("game/sprites/sprite.png");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .player.animating {
            position: fixed;
            z-index: 1000;
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
                    <form method="POST" action="game/scripts/solveur.php">
                        <input type="hidden" name="level_name" value="<?= $_GET["level"] ?? 1 ?>">
                        <input type="hidden" name="current_state" id="currentState">
                        <button type="submit"><img src="icons/point-dinterrogation.png" alt="Hint"></button>
                    </form>

                    <a>
                        <img class="parameters" src="icons/parametres.png" alt="Parameters">
                    </a>
                    </div>

                </div>
                <div id="grid" class="grid"></div>


                <?php
                    unset($_SESSION["solution"]);
                    // Récupérer l'état sauvegardé s'il existe
                    $savedState = null;
                    if (isset($_SESSION['current_state'])) {
                        $savedState = json_decode($_SESSION['current_state'], true);
                        unset($_SESSION['current_state']);
                    }
                ?>
            </div>
            <div class="dropdown" id="leaderboard">
                <table>/td>
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

                    </tbody>
                </table>
            </div>
            
        
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

        const savedState = <?= $savedState ? json_encode($savedState) : 'null' ?>;

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
                
                // Mettre à jour les références originales
                rows = savedLevelData.rows;
                cols = savedLevelData.cols;

                originalPlayerPos = {...savedLevelData.playerPos}; // PROBLEME ICI
                originalPlayground = JSON.parse(JSON.stringify(savedLevelData.playground));
                
                // Initialiser avec l'état sauvegardé
                playerPos = {...originalPlayerPos};
                playground = JSON.parse(JSON.stringify(originalPlayground));

                console.log(playground);
                
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
                clonedPlayground[playerPos.row][playerPos.col] = PLAYER;
                
                // Aplatir le tableau 2D en 1D et ajouter rows/cols devant
                const flatPlayground = clonedPlayground.flat();
                const stateToSend = [rows, cols, ...flatPlayground];
                
                document.getElementById('currentState').value = JSON.stringify(stateToSend);
            });


            function initGame() {
                // Réinitialisation à l'état original
                playerPos = {...originalPlayerPos};
                playground = JSON.parse(JSON.stringify(originalPlayground));
                

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

                        if (playground[row][col] === VISITED || (col === playerPos.col && row === playerPos.row)) {
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
                                initGame();
                                let moves = parseInt(sessionStorage.getItem("moves")) || 0;
                                moves++;
                                sessionStorage.setItem("moves", moves.toString());
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
                initGame();
            });

            document.addEventListener('keydown', (e) => {
                switch (e.key) {
                    case 'ArrowUp': slide('up'); break;
                    case 'ArrowDown': slide('down'); break;
                    case 'ArrowLeft': slide('left'); break;
                    case 'ArrowRight': slide('right'); break;
                }
            });
        });

</script>
    </main>
    <footer>
        <?php include 'php/footer.php'; ?>
    </footer>
</body>
</html>