<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur de Niveaux</title><span> <a href="play.php">Jeu</a></span>
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

        .grid-config {
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .grid-config input {
            width: 60px;
            padding: 5px;
            text-align: center;
        }

        .grid {
            display: grid;
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
            border: 1px solid #aaa;
            aspect-ratio: 1;
        }

        .cell.wall {
            background-color: #333;
        }

        .cell.dragover {
            outline: 2px dashed #4CAF50;
        }

        .player {
            /* background-color: #FF9800; */
            /*border-radius: 50%;*/
            width: 100%;
            height: 100%;
            /*box-shadow: 0 0 5px #FF9800;*/
            position: absolute;
            z-index: 10;
            transition: transform 0.3s none;
            background-image: url("../sprites/sprite.png");
            background-size: cover;
            background-repeat: no-repeat;
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
            background-color: rgba(255, 152, 0, 0.3);
        }

        .cell.visited .fill {
            transform: scale(1);
        }

        .tp {
            background-image: url("../sprites/tp.png");
            background-size: cover;
            background-repeat: no-repeat;
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 10;
        }

        .path {
            background-color: #2196F3;
        }

        .hole{
            background-color:red;
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
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #9f8b77;
        }

        .instructions {
            margin-top: 20px;
            max-width: 400px;
            text-align: center;
            color: #555;
            font-size: 14px;
            line-height: 1.5;
        }

        #toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        #toolbar div {
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: grab;
            font-size: 14px;
            min-width: 60px;
            text-align: center;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }

        .size-warning {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message {
            color: #d32f2f;
            margin-top: 10px;
            font-weight: bold;
            min-height: 20px;
        }
    </style>
</head>

<body>
    <h1>Éditeur de Niveaux</h1>
    
    <div id="toolbar"></div>
    
    <div class="grid-config">
        <div>
            <input type="number" id="rows" placeholder="Lignes" value="10" min="5" max="20">
            <div class="size-warning" id="row-warning">(5-20)</div>
        </div>
        <div>
            <input type="number" id="cols" placeholder="Colonnes" value="10" min="5" max="20">
            <div class="size-warning" id="col-warning">(5-20)</div>
        </div>
        <button onclick="generateGrid()">Générer la grille</button>
    </div>
    
    <div class="error-message" id="error-message"></div>
    
    <div class="game-container">
        <div class="grid" id="grid"></div>
    </div>
    
    <div class="controls">
        <button id="new-game">Nouvelle grille</button>
        <button id="export-json">Exporter JSON</button>
        <form method="POST" action="../scripts/solveur.php">
            <input type="hidden" name="level_name" value="<?= $_GET["level"] ?? 1 ?>"> <!-- Remplacer par l'état actuel de la grille -->
            <button type="submit">Run the solver</button>
        </form>
        <?php
            if (isset($_SESSION["solution"])) echo "Shortest path from your position in order to complete this level: " . $_SESSION["solution"];
            unset($_SESSION["solution"]); // Pour éviter de l'afficher à chaque fois
        ?>
    </div>
    Lancer le solveur fait refresh la page donc peut-être exécuter le solveur dans ce fichier
    
    <div class="instructions">
        <p><strong>Instructions :</strong></p>
        <p>1. Choisissez les dimensions (5-20 lignes/colonnes)</p>
        <p>2. Glissez-déposez les éléments depuis la barre d'outils</p>
        <p>3. Cliquez sur une case pour supprimer un élément</p>
        <p>4. Exportez votre création avec le bouton "Exporter JSON"</p>
    </div>
    
    <script>
        let ROWS = 10;
        let COLS = 10;
        let currentPlayerCell = null;
        
        const ELEMENTS = [
            { type: "player", label: "Joueur", color: "#FF5722" },
            { type: "wall", label: "Mur", color: "#333" },
            { type: "tp", label: "Téléporteur", color: "#9C27B0" },
            { type: "path", label: "Chemin", color: "#2196F3" },
            { type: "hole", label: "Trou", color: "red" }
        ];
        
        function initToolbar() {
            const toolbar = document.getElementById('toolbar');
            ELEMENTS.forEach(el => {
                const btn = document.createElement('div');
                btn.textContent = el.label;
                btn.draggable = true;
                btn.style.background = el.color;
                btn.dataset.type = el.type;
                
                btn.addEventListener('dragstart', e => {
                    e.dataTransfer.setData('element-type', el.type);
                });
                
                toolbar.appendChild(btn);
            });
        }
        
        function validateInputs() {
            const rowsInput = document.getElementById('rows');
            const colsInput = document.getElementById('cols');
            const rowWarning = document.getElementById('row-warning');
            const colWarning = document.getElementById('col-warning');
            
            rowsInput.addEventListener('input', () => {
                if (rowsInput.value < 5 || rowsInput.value > 20) {
                    rowWarning.style.display = 'block';
                } else {
                    rowWarning.style.display = 'none';
                }
            });
            
            colsInput.addEventListener('input', () => {
                if (colsInput.value < 5 || colsInput.value > 20) {
                    colWarning.style.display = 'block';
                } else {
                    colWarning.style.display = 'none';
                }
            });
        }
        
        function generateGrid() {
            ROWS = Math.max(5, Math.min(20, parseInt(document.getElementById('rows').value) || 10));
            COLS = Math.max(5, Math.min(20, parseInt(document.getElementById('cols').value) || 10));
            
            document.getElementById('rows').value = ROWS;
            document.getElementById('cols').value = COLS;
            
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            currentPlayerCell = null;
            document.getElementById('error-message').textContent = '';
            
            grid.style.gridTemplateColumns = `repeat(${COLS}, 1fr)`;
            grid.style.gridTemplateRows = `repeat(${ROWS}, 1fr)`;
            
            for(let i = 0; i < ROWS; i++) {
                for(let j = 0; j < COLS; j++) {
                    const cell = document.createElement('div');
                    cell.className = 'cell';
                    cell.dataset.row = i;
                    cell.dataset.col = j;
                    
                    cell.ondragover = e => e.preventDefault();
                    cell.ondrop = onDropElement;
                    cell.onclick = handleCellClick;
                    
                    cell.ondragover = function(e) {
                        e.preventDefault();
                        this.classList.add('dragover');
                    };
                    
                    cell.ondragleave = function(e) {
                        this.classList.remove('dragover');
                    };
                    
                    cell.innerHTML = '<div class="fill"></div>';
                    grid.appendChild(cell);
                }
            }
        }
        
        function onDropElement(e) {
            e.preventDefault();
            const type = e.dataTransfer.getData('element-type');
            if (!type) return;

            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = '';
            this.classList.remove('dragover');

            if (type === 'player') {
                if (currentPlayerCell && currentPlayerCell !== this) {
                    errorMessage.textContent = 'Un seul joueur est autorisé.';
                    return;
                }
            }

            if (type === 'tp') {
                const currentTPs = document.querySelectorAll('[data-element="tp"]');
                const wasTP = this.dataset.element === 'tp';
                const newTPCount = currentTPs.length - (wasTP ? 1 : 0) + 1;

                if (newTPCount > 2) {
                    errorMessage.textContent = 'Maximum 2 téléporteurs autorisés.';
                    return;
                }
            }

            this.innerHTML = '<div class="fill"></div>';
            this.classList.remove('wall', 'player', 'tp', 'path');
            delete this.dataset.element;

            this.dataset.element = type;
            
            switch(type) {
                case "player":
                    currentPlayerCell = this;
                    const playerDiv = document.createElement('div');
                    playerDiv.className = 'player';
                    this.appendChild(playerDiv);
                    break;
                case "wall":
                    this.classList.add('wall');
                    break;
                case "tp":
                    const tpDiv = document.createElement('div');
                    tpDiv.className = 'tp';
                    this.appendChild(tpDiv);
                    break;
                case "path":
                    this.classList.add('path');
                    break;
                case "hole":
                    this.classList.add('hole');
                    break;
            }
        }
        
        function handleCellClick() {
            if (this.dataset.element) {
                if (this.dataset.element === 'player') {
                    currentPlayerCell = null;
                }
                this.innerHTML = '<div class="fill"></div>';
                this.classList.remove('wall', 'player', 'tp', 'path','hole');
                delete this.dataset.element;
            }
        }

        const EMPTY = -1;
        const VISITED = 0;
        const PATH = 1;
        const WALL = 2;
        const TP = 3;
        const HOLE = 4;
        const PLAYER = 5;

        
        
        function exportLevelJSON() {
            const cells = document.querySelectorAll('.cell');
            const tabdata = [];

            let hasPlayer = false;
            const teleporters = [];

            cells.forEach(cell => {
                const row = parseInt(cell.dataset.row);
                const col = parseInt(cell.dataset.col);
                const elementType = cell.dataset.element;

                let elementCode = EMPTY; // Par défaut : EMPTY

                if (elementType) {
                    switch (elementType) {
                        case 'player':
                            elementCode = PLAYER;
                            hasPlayer = true;
                            break;
                        case 'wall':
                            elementCode = WALL;
                            break;
                        case 'tp':
                            elementCode = TP;
                            teleporters.push({ row, col });
                            break;
                        case 'path':
                            elementCode = PATH;
                            break;
                        case 'hole':
                            elementCode = HOLE;
                            break;
                        // Ajoute d'autres cas pour les différents types d'éléments
                    }
                }
                tabdata.push(elementCode);
            });

            if (teleporters.length > 0 && teleporters.length !== 2) {
                document.getElementById('error-message').textContent = 'Nombre de téléporteurs invalide (0 ou 2 requis)';
                return;
            }

            if (!hasPlayer) {
                document.getElementById('error-message').textContent = 'Attention : Votre niveau n\'a pas de joueur !';
                return;
            }

            const levelArray = [ROWS, COLS, ...tabdata];
            const levelJSON = JSON.stringify(levelArray, null, 2);

            const blob = new Blob([levelJSON], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `niveau_${ROWS}x${COLS}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            initToolbar();
            validateInputs();
            generateGrid();
            
            document.getElementById('export-json').addEventListener('click', exportLevelJSON);
            document.getElementById('new-game').addEventListener('click', generateGrid);
        });
    </script>
</body>
</html>