<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Sign </title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>

<body>
    <header>
        <?php require_once "header1.php"; ?>
    </header>

    <main id="leaderboard-main">
        <div class="dbleaderboard-gradient-wrapper">
            <div class="dbleaderboard-container">
                <table class="dbleaderboard">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Time</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Valeur 1</td>
                            <td>Valeur 2</td>
                            <td>Valeur 3</td>
                        </tr>
                        <tr>
                            <td>Valeur 4</td>
                            <td>Valeur 5</td>
                            <td>Valeur 6</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- <script>
    
// Fond animé du leaderboard

    (function animateLeaderboardBg() {
  const el = document.getElementById('leaderboard-main');
  if (!el) return;
  let t = 0;
  function animate() {
    t += 0.008; // vitesse
    // Génère des positions qui bougent en cercle ou en vague
    const x1 = 30 + 10 * Math.sin(t);
    const y1 = 70 + 10 * Math.cos(t * 1.2);
    const x2 = 90 + 5 * Math.cos(t * 1.1);
    const y2 = 10 + 10 * Math.sin(t * 1.3);
    const x3 = 90 + 8 * Math.sin(t * 0.7);
    const y3 = 40 + 8 * Math.cos(t * 0.9);
    const x4 = 85 + 10 * Math.cos(t * 0.5);
    const y4 = 85 + 10 * Math.sin(t * 0.6);
    const x5 = 60 + 10 * Math.sin(t * 1.5);
    const y5 = 90 + 5 * Math.cos(t * 1.7);
    const x6 = 20 + 8 * Math.cos(t * 1.4);
    const y6 = 90 + 8 * Math.sin(t * 1.1);
    const x7 = 75 + 7 * Math.sin(t * 0.8);
    const y7 = 10 + 7 * Math.cos(t * 1.2);
    const x8 = 10 + 10 * Math.cos(t * 1.3);
    const y8 = 20 + 10 * Math.sin(t * 1.5);

    el.style.backgroundImage = `
      radial-gradient(at ${x1}% ${y1}%, rgba(1, 18, 41, 0.5) 0, transparent 50%),
      radial-gradient(at ${x2}% ${y2}%, rgba(1, 41, 11, 0.5) 0, transparent 50%),
      radial-gradient(at ${x3}% ${y3}%, rgba(119, 34, 115, 0.6) 0, transparent 50%),
      radial-gradient(at ${x4}% ${y4}%, #110a58b0 0, rgba(31, 21, 88, 0.3) 50%),
      radial-gradient(at ${x5}% ${y5}%, rgba(20, 40, 80, 0.7) 0, transparent 50%),
      radial-gradient(at ${x6}% ${y6}%, #051c3f 0, transparent 50%),
      radial-gradient(at ${x7}% ${y7}%, #102840 0, transparent 50%),
      radial-gradient(at ${x8}% ${y8}%, rgba(70, 18, 65, 0.8) 0, transparent 50%)
    `;
    requestAnimationFrame(animate);
  }
  animate();
})();
</script> -->
</body>
</html>
