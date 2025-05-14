<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css" integrity="sha384-NvKbDTEnL+A8F/AA5Tc5kmMLSJHUO868P+lDtTpJIeQdGYaUIuLr4lVGOEA1OcMy" crossorigin="anonymous">
    </head>
<body>
    <div class='cursor' id="cursor"></div>
    <header>
        <?php include 'php/header1.php'; ?>
    </header>
    <main id="index">
        <div>
            <button onclick="window.location.href='play.php';">Get Started</button>
            <a><img src="icons/double-arrow-white.png" id="scroll-icon"></a>
        </div>
   
        <div id="ancre-scroll">
            <h1>Game mechanics</h1>
        </div>
        <div class="card-container">
            <div class="card-wrapper">
                <div class="card">
                    <h2>Card 1</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </div>
            </div>  
            <div class="card-wrapper">
                <div class="card">
                    <h2>Card 2</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </div>
            </div>
            <div class="card-wrapper">
                <div class="card">
                    <h2>Card 3</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
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