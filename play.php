<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Play</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css" integrity="sha384-NvKbDTEnL+A8F/AA5Tc5kmMLSJHUO868P+lDtTpJIeQdGYaUIuLr4lVGOEA1OcMy" crossorigin="anonymous">
    </head>
<body>
    <div class="menu-icon" onclick="toggleDropdown()">
    🏆
    </div>
    <header>
        <a href="index.php"><img src="#"></a>
        <nav>
            <a href="#about" class="linkanimation">Leaderboard</a>
            <a href="php/contact.php" class="linkanimation">Contact</a>
            <a href="php/connexion.php" class="linkanimation">Sign in</a>
            <script src="script.js"></script>
        </nav>
    </header>
    <main id="play">
        <div class="game-wrapper">
            <div class="game-container">
                <div id="grid" class="grid"></div>
            </div>
            <div class="dropdown" id="leaderboard">
                <table>
                    <thead>
                        <tr>
                            <th colspan="3">Leaderboard 🏆</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Moves</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>🥇</td>
                            <td>Alice</td>
                            <td>150</td>
                        </tr>
                        <tr>
                            <td>🥈</td>
                            <td>Bob</td>
                            <td>120</td>
                        </tr>
                        <tr>
                            <td>🥉</td>
                            <td>Charlie</td>
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
                            <td>Natypeno</td>
                            <td>85</td>
                        </tr>

                    </tbody>
                </table>
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
        dropdown.classList.add('open'); // Ouvre le menu
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
</script>
    </main>
    <footer>
        <?php include 'php/footer.php'; ?>
    </footer>
</body>
</html>