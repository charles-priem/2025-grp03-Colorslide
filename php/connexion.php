<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body class="connexion">
    <header>
    <?php require_once "header1.php"; ?>
    </header>
    <main id="connexion-main">
        <section>
            <video autoplay muted loop id="background-video">
        <source src="../images/video.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la balise vidéo.
             </video>     
    <div class="login-box">
        <form action="">
            <h2>Login</h2>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon>
                </span>
                <input type="email" required>
                <label>Email</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" required>
                <label>Password</label>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">remember me
                </label>
        <a href="#">Forgot password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="register-link">
            <p>Don't have an account?<a href="#">Register</a></p>
        </div>
        <!--<div class="form-box register">
            <form action="">
                <h2>Register</h2>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-circle-outline"></ion-icon>
                </span>
                    <input type="text" required>
                    <label>Name</label>
                </div>-->
        <h2>Register</h2>
         <!--partie sensible au css-->
        <div class="input-box">
                <span class="icon"><ion-icon name="person-outline"></ion-icon>
                </span>
                <input type="email" required>
                <label>Name</label>
        </div>
        <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon>
                </span>
                <input type="email" required>
                <label>Email</label>
            </div>
        <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" required>
                <label>Password</label>
            </div>
            

                
        
    </form>
    </div>
</section>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- Script pour ralentir la vidéo -->
    <script>
        const videoElement = document.getElementById('background-video');

        if (videoElement) {
            
            videoElement.addEventListener('canplay', function() {
            
                this.playbackRate = 0.70; //  ajuster selon les préférences
            });

            if (videoElement.readyState >= 3) { // HAVE_FUTURE_DATA ou HAVE_ENOUGH_DATA
                 videoElement.playbackRate = 0.75;
            }

        } else {
            console.warn('Element video avec ID "background-video" non trouvé.');
        }
    </script>
</main>   

<footer> <?php require_once "footer.php"; ?> </footer>

     

</html>
