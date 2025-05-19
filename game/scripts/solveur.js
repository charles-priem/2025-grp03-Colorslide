const EMPTY = -1;
const VISITED = 0;
const PATH = 1;
const WALL = 2;
const TP = 3;
const HOLE = 4;
const PLAYER = 5;

function decodeJSON(data) {
    const rows = data.rows;
    const cols = data.cols;
    let playerPos = [0, 0];
    const playground = Array(rows).fill().map(() => Array(cols).fill(EMPTY));
    const conv = {
        "path": PATH,
        "wall": WALL,
        "tp": TP,
        "hole": HOLE,
        "player": VISITED
    };
    for (const e of data.elements) {
        playground[e.row][e.col] = conv[e.type];
        if (e.type === "player") {
            playerPos = [e.row, e.col];
        }
    }
    
    return { rows, cols, playerPos, playground };
}

function solveLevel(levelData) {
    const { rows, cols, playerPos, playground } = decodeJSON(levelData);
    
    // Trouver les téléporteurs
    const tps = [];
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            if (playground[r][c] === TP) {
                tps.push([r, c]);
            }
        }
    }
    
    // Directions possibles avec leur équivalent pour la sortie
    const directions = [
        { key: "up", char: "N", dr: -1, dc: 0 },
        { key: "right", char: "E", dr: 0, dc: 1 },
        { key: "down", char: "S", dr: 1, dc: 0 },
        { key: "left", char: "O", dr: 0, dc: -1 }
    ];
    
    /**
     * Simule un mouvement dans une direction
     * @param {string} direction - Direction du mouvement
     * @param {Array} pos - Position actuelle [row, col]
     * @param {Array} playground - État actuel du terrain
     * @returns {Object|null} - Nouvelle position et cases modifiées, ou null si mouvement impossible
     */
    function simulateMove(direction, pos, playground) {
        const dir = directions.find(d => d.key === direction);
        if (!dir) return null;
        
        const { dr, dc } = dir;
        let [r, c] = pos;
        const modifiedCells = [];
        
        while (true) {
            const nr = r + dr;
            const nc = c + dc;
            
            // Vérifier si on est sorti du terrain
            if (nr < 0 || nr >= rows || nc < 0 || nc >= cols) {
                break;
            }
            
            const cell = playground[nr][nc];
            
            // Vérifier si on a rencontré un mur
            if (cell === WALL) {
                break;
            }
            
            // Téléportation
            if (cell === TP) {
                for (const [tr, tc] of tps) {
                    if (tr !== nr || tc !== nc) {
                        r = tr;
                        c = tc;
                        break;
                    }
                }
                continue;
            }
            
            // Vérifier si on a rencontré un trou
            if (cell === HOLE) {
                return null;
            }
            
            // Marquer la case comme visitée
            if (playground[nr][nc] === PATH) {
                modifiedCells.push([nr, nc]);
            }
            
            r = nr;
            c = nc;
        }
        
        // Si la position n'a pas changé, le mouvement n'est pas valide
        if (r === pos[0] && c === pos[1]) {
            return null;
        }
        
        return { newPos: [r, c], modifiedCells };
    }
    
    /**
     * Vérifie si toutes les cases PATH ont été visitées
     * @param {Array} playground - État actuel du terrain
     * @returns {boolean} - Vrai si le niveau est résolu
     */
    function isSolved(playground) {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                if (playground[r][c] === PATH) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Génère une clé unique pour un état du jeu
     * @param {Array} pos - Position du joueur
     * @param {Array} playground - État du terrain
     * @returns {string} - Clé unique
     */
    function stateKey(pos, playground) {
        let key = `${pos[0]},${pos[1]}|`;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                key += playground[r][c];
            }
        }
        return key;
    }
    
    // File pour le BFS
    const queue = [{ pos: playerPos, playground: JSON.parse(JSON.stringify(playground)), moves: [] }];
    
    // Ensemble des états déjà visités
    const visitedStates = new Set();
    
    // Tant que la file n'est pas vide
    while (queue.length > 0) {
        const current = queue.shift();
        
        // Générer une clé unique pour cet état
        const key = stateKey(current.pos, current.playground);
        
        // Si l'état a déjà été visité, passer au suivant
        if (visitedStates.has(key)) {
            continue;
        }
        
        // Marquer l'état comme visité
        visitedStates.add(key);
        
        // Si le jeu est résolu, retourner la solution
        if (isSolved(current.playground)) {
            // Convertir les mouvements au format demandé (NEOSO)
            let solution = "";
            for (const move of current.moves) {
                const dir = directions.find(d => d.key === move);
                solution += dir.char;
            }
            return solution;
        }
        
        // Essayer chaque direction
        for (const { key } of directions) {
            // Simulation du mouvement
            const result = simulateMove(key, current.pos, current.playground);
            
            // Si le mouvement est impossible
            if (!result) {
                continue;
            }
            
            // Création d'une copie du terrain pour appliquer les changements
            const newPlayground = JSON.parse(JSON.stringify(current.playground));
            
            // Appliquer les modifications sur le terrain
            for (const [r, c] of result.modifiedCells) {
                newPlayground[r][c] = VISITED;
            }
            
            // Ajouter le nouvel état à la file
            queue.push({
                pos: result.newPos,
                playground: newPlayground,
                moves: [...current.moves, key]
            });
        }
    }
    
    // Si on arrive ici, c'est qu'aucune solution n'a été trouvée
    return null;
}

/**
 * Exécute le solveur sur un fichier de niveau
 * @param {string} levelFilePath - Chemin vers le fichier JSON du niveau
 * @returns {Promise<string>} - Solution ou "null" si pas de solution
 */
function runVerif(levelFilePath) {
    return new Promise((resolve, reject) => {
        // Charger le fichier JSON
        fetch(levelFilePath)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur lors du chargement du niveau: ${response.status}`);
                }
                return response.json();
            })
            .then(levelData => {
                // Résoudre le niveau
                const solution = solveLevel(levelData);
                
                // Créer le fichier sol.txt avec le résultat
                const solutionText = solution || "null";
                
                // En environnement navigateur, on ne peut pas directement créer un fichier
                // On simule donc la création du fichier en stockant la solution dans le localStorage
                localStorage.setItem('sol.txt', solutionText);
                
                // On peut aussi proposer de télécharger le fichier sol.txt
                const blob = new Blob([solutionText], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.style.display = 'none';
                link.href = url;
                link.download = 'sol.txt';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Résoudre la promesse avec la solution
                resolve(solutionText);
            })
            .catch(error => {
                console.error("Erreur:", error);
                reject(error);
            });
    });
}