<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .game-container {
            position: relative;
            width: 400px;
            height: 400px;
            background-color: #fff;
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
            /* gap: 2px; */
        }

        .cell {
            background-color: #ddd;
            /* border-radius: 2px; */
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-user-select: none;
            user-select: none;
            position: relative;
            overflow: hidden;
        }

        .cell.wall {
            background-color: #333;
        }

        .player {
            background-color: #FF9800;
            /*border-radius: 50%;*/
            width: 100%;
            height: 100%;
            /*box-shadow: 0 0 5px #FF9800;*/
            position: absolute;
            z-index: 10;
            transition: transform 0.3s none;
            /* background-image: url("../sprites/sprite.png");
            background-size: cover;
            background-repeat: no-repeat; */
        }

        .player.animating {
            position: fixed;
            z-index: 1000;
            /* Make sure it's above everything */
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
            /*transition: transform 0.5s linear;*/
            z-index: 1;
        }

        .cell.visited .fill {
            /* background-image: url("../sprites/test-trail.png");
            background-size: cover;
            background-repeat: no-repeat; */
            background-color: #00BFFF;
            transform: scale(1);
            display: flex;
        }

        .controls {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #8f7a66;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #9f8b77;
        }

        .instructions {
            margin-top: 20px;
            max-width: 400px;
            text-align: center;
            color: #555;
        }
        
        .tp {
            /* border-radius: 50%; */
            background-color: purple; 
            /*background-image: url("../sprites/tp.png");
            background-size: cover;
            background-repeat: no-repeat;*/
        }

        .hole {
            background-color: white;
            border : 1px #333 solid;
        }

    </style>
</head>

<body>
    <h1>Color Slide</h1>
    <div class="game-container">
        <div class="grid" id="grid"></div>
    </div>
    <div class="controls">
        <button id="new-game">Nouvelle partie</button>
    </div>

    <script>
        const EMPTY = -1;
        const VISITED = 0;
        const PATH = 1;
        const WALL = 2;
        const TP = 3;
        const HOLE = 4;
        
        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.getElementById('grid');
            const newGameButton = document.getElementById('new-game');

            const rows = 10;
            const cols = 10;
            const cellSize = 400 / 6; // Taille d'une case en pixels
            let playerPos = { row: 1, col: 1 };
            let playground = [];
            let isMoving = false;
            let animationFrameId = null;
            let TPs = [];
            let moves = 0;

            // Initialisation du jeu
            function initGame() {
                // Annuler toute animation en cours
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                }

                grid.innerHTML = '';
                moves = 0;
                TPs = []; // Important: Réinitialiser les téléporteurs à chaque nouvelle partie
                
                playerPos = { row: 1, col: 1 };

                playground = [
                    [WALL, WALL, WALL, WALL, WALL, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, HOLE, PATH, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, WALL, WALL, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, WALL, WALL, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL,PATH, PATH,PATH,PATH,WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL,WALL,WALL,WALL,WALL,WALL, EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY]
                ]


                playground1 = [
                    [WALL, WALL, WALL, WALL, WALL, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, PATH, PATH, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, WALL, WALL, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, WALL, WALL, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, PATH, PATH, PATH, PATH, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [WALL, WALL, WALL, WALL, WALL, WALL,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY],
                    [EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY,EMPTY]
                ]

                playground2 = [
                    [EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY],
                    [EMPTY, WALL,  WALL,  WALL,  WALL,  EMPTY, EMPTY, EMPTY, EMPTY, EMPTY],
                    [EMPTY, WALL,  PATH,  TP,    WALL,  EMPTY, EMPTY, EMPTY, EMPTY, EMPTY],
                    [EMPTY, WALL,  WALL,  WALL,  WALL,  EMPTY, EMPTY, EMPTY, EMPTY, EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, WALL,  WALL,  WALL,  EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, EMPTY, WALL,  PATH,  WALL,  EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, WALL,  WALL,  WALL,  PATH,  WALL,  EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, WALL,  TP,    PATH,  PATH,  WALL,  EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, WALL,  PATH,  PATH,  PATH,  WALL,  EMPTY],
                    [EMPTY, EMPTY, EMPTY, EMPTY, WALL,  WALL,  WALL,  WALL,  EMPTY, EMPTY],
                ];

                // Détecter et enregistrer les positions des téléporteurs
                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        if (playground[row][col] === TP) {
                            TPs.push({ row, col });
                        }
                    }
                }

                isMoving = false;

                // Création de la grille
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

                        // Ajouter l'effet de remplissage
                        const fill = document.createElement('div');
                        fill.className = 'fill';
                        cell.appendChild(fill);

                        if (playground[row][col] === VISITED || (col === playerPos.col && row === playerPos.row) || playground[row][col] === VISITED ) {
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

            // Animation de déplacement 
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

                    // Marquage des cases visitées pendant l'animation
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

            // Marquer les cases visitées
            function markVisitedCells(startPos, endPos, direction, progress) {
                // Ne pas marquer si on arrive à un téléporteur
                if (playground[endPos.row][endPos.col] === TP) {
                    // Pour le chemin jusqu'au TP, on marque seulement les cases PATH
                    const totalSteps = Math.max(
                        Math.abs(endPos.row - startPos.row),
                        Math.abs(endPos.col - startPos.col)
                    ) - 1; // -1 car on ne marque pas le TP lui-même
                    
                    // Calculer combien de pas on marque avec le progrès actuel
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

                        // Ne pas dépasser les limites
                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;
                        
                        // S'arrêter si on arrive au TP
                        if (playground[row][col] === TP) break;

                        // Marquer comme visité seulement si c'est un chemin
                        if (playground[row][col] === PATH) {
                            playground[row][col] = VISITED;
                            const cell = grid.children[row * cols + col];
                            cell.classList.add('visited');
                        }
                    }
                } else {
                    // Comportement normal pour les autres déplacements
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

                        // Ne pas dépasser les limites
                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;

                        // Marquer comme visité seulement si c'est un chemin
                        if (playground[row][col] === PATH) {
                            playground[row][col] = VISITED;
                            const cell = grid.children[row * cols + col];
                            cell.classList.add('visited');
                        }
                    }
                }
            }

            // Animation de chute dans le trou
            function fallIntoHole(callback) {
                const playerElement = document.querySelector('.player');
                if (!playerElement) return;

                playerElement.classList.add('falling');
                
                checkWin();

                // Après l'animation, réinitialiser le jeu
                setTimeout(() => {
                    if (playerElement.parentNode) {
                        playerElement.parentNode.removeChild(playerElement);
                    }
                    callback();
                }, 500); // Correspond à la durée de l'animation
            }
            
            // Déplace le joueur jusqu'au mur ou téléporteur
            function slide(direction) {
                if (isMoving) return;
                isMoving = true;
                let fallen = false;
                let newRow = playerPos.row;
                let newCol = playerPos.col;
                let moved = false;
                const startPos = { ...playerPos };
                let teleportDestination = null;
                
                // Première étape : trouver où le joueur s'arrête
                while (true) {
                    let nextRow = newRow;
                    let nextCol = newCol;

                    switch (direction) {
                        case 'up': nextRow--; break;
                        case 'down': nextRow++; break;
                        case 'left': nextCol--; break;
                        case 'right': nextCol++; break;
                    }

                    // Vérifier les limites et les murs
                    if (nextRow < 0 || nextRow >= rows || nextCol < 0 || nextCol >= cols || 
                        playground[nextRow][nextCol] === WALL) {
                        break;
                    }

                    // On trouve un téléporteur
                    if (playground[nextRow][nextCol] === TP) {
                        // Trouver l'autre téléporteur
                        const otherTP = TPs.find(tp => !(tp.row === nextRow && tp.col === nextCol));
                        
                        if (otherTP) {
                            teleportDestination = { 
                                source: { row: nextRow, col: nextCol },
                                destination: otherTP
                            };
                            
                            newRow = nextRow; // Position du premier TP
                            newCol = nextCol;
                            moved = true;
                            break; // Important: s'arrêter au TP, pas continuer
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
                    // Animation du mouvement initial (jusqu'au TP ou à la destination finale)
                    animateMovement(startPos, { row: newRow, col: newCol }, direction, () => {
                        // Cas de téléportation
                        if (teleportDestination) {
                            // Mettre à jour la position du joueur à la sortie du téléporteur
                            const tpDest = teleportDestination.destination;
                            
                            // Mettre à jour l'élément visuel du joueur
                            const oldCell = grid.children[newRow * cols + newCol];
                            const newCell = grid.children[tpDest.row * cols + tpDest.col];
                            const player = document.querySelector('.player');
                            
                            if (player && oldCell.contains(player)) {
                                oldCell.removeChild(player);
                                newCell.appendChild(player);
                            }
                            
                            // Mettre à jour la position interne du joueur
                            playerPos.row = tpDest.row;
                            playerPos.col = tpDest.col;
                            
                            // Vérifier si on peut continuer après le TP
                            let afterTPRow = tpDest.row;
                            let afterTPCol = tpDest.col;
                            
                            switch (direction) {
                                case 'up': afterTPRow--; break;
                                case 'down': afterTPRow++; break;
                                case 'left': afterTPCol--; break;
                                case 'right': afterTPCol++; break;
                            }
                            
                            isMoving = false;
                            
                            // Si on peut continuer après la téléportation
                            if (afterTPRow >= 0 && afterTPRow < rows && 
                                afterTPCol >= 0 && afterTPCol < cols && 
                                playground[afterTPRow][afterTPCol] !== WALL) {
                                slide(direction); // Continuer dans la même direction
                            } else {
                                checkWin();
                            }
                        } 
                        else if (fallen) {
                            fallIntoHole(() => {
                                playerPos.row = 3;
                                playerPos.col = 1;
                                isMoving = false;
                                initGame();
                            });
                        } 
                        else {
                            playerPos.row = newRow;
                            playerPos.col = newCol;
                            isMoving = false;
                            checkWin();
                        }
                        moves++;
                    });
                } else {
                    isMoving = false;
                }
            }

            // Vérifie si le joueur a gagné
            function checkWin() {
                let NotVisited = playground.some(l => l.some(n => n === PATH));
                if (!NotVisited) setTimeout(() => {
                    //alert(`Félicitations ! Vous avez rempli toute la grille en ${moves} mouvements !`);
                }, 50);
            }

            // Écouteurs d'événements 
            newGameButton.addEventListener('click', initGame);

            document.addEventListener('keydown', (e) => {
                switch (e.key) {
                    case 'ArrowUp': slide('up'); break;
                    case 'ArrowDown': slide('down'); break;
                    case 'ArrowLeft': slide('left'); break;
                    case 'ArrowRight': slide('right'); break;
                }
            });

            // Démarrer le jeu 
            initGame();
        });
    </script>
</body>

</html>