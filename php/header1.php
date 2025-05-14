        <?php
        if (session_status() === PHP_SESSION_NONE) session_start();
        ?>
        <a href="../index.php"><img src="#"></a>
        <nav>
            <a href="../php/leaderboard.php" class="linkanimation">Leaderboard</a>
            <a href="../php/contact.php" class="linkanimation">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../php/dashboard.php" class="linkanimation">Profile</a>
            <?php else: ?>
                <a href="../php/connexion.php" class="linkanimation">Sign in</a>
            <?php endif; ?>
            <script src="../script.js"></script>
        </nav>
        