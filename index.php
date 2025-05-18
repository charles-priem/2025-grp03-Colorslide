<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css" integrity="sha384-NvKbDTEnL+A8F/AA5Tc5kmMLSJHUO868P+lDtTpJIeQdGYaUIuLr4lVGOEA1OcMy" crossorigin="anonymous">
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
</head>

<script>
    // Script du cusrseur custom
  document.addEventListener("DOMContentLoaded", function() {
    var cursor = document.getElementById("cursor");
    document.body.addEventListener("mousemove", function(e) {
      cursor.style.left = e.clientX + "px";
      cursor.style.top = e.clientY + "px";
    });
  });
</script>

<body>
    <header>
        <?php include 'php/header1.php'; ?>
    </header>
    <div class='cursor' id="cursor"></div>
    <main id="index">
        <div>
            <button onclick="window.location.href='play.php';">Get Started</button>
            <a href="#"><img src="icons/double-arrow-white.png" id="scroll-icon"></a>
        </div>
   
        <div id="ancre-scroll">
            <h1>Game mechanics</h1>
        </div>
        <div class="card-container">
            <div class="card-wrapper">
                <div class="card">
                    <img src="videos/Deplacements.gif" alt="Card Image">
                    <h2>Movement</h2>
                    <p>The player can move up, down, left, or right, and keeps sliding in that direction until hitting a wall. As he moves, he paints the floor ; the goal is to paint every tile in the maze.</p>
                </div>
            </div>  
            <div class="card-wrapper">
                <div class="card">
                    <img src="videos/Hole.gif" alt="Card Image">
                    <h2>Holes</h2>
                    <p>Holes are traps in the maze. If the player slides into one, he falls and die, forcing a restart. Players must avoid holes by planning their moves carefully.</p>
                </div>
            </div>
            <div class="card-wrapper">
                <div class="card">
                    <img src="videos/TP.gif" alt="Card Image">
                    <h2>Teleporters</h2>
                    <p>Teleporters come in pairs and instantly move the player from one to the other. When the player slides into a teleporter, he appears at its linked exit and keep moving in the same direction.</p>
                </div>
            </div>
        </div>
        <video autoplay loop muted plays-inline class="background-clip">
            <source src="images/video.mp4" type="video/mp4">
        </video>
    </main>
    <footer>
        <?php include 'php/footer.php'; ?>
    </footer>
</body>
</html>