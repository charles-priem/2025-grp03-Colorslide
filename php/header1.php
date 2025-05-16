        <?php
        if (session_status() === PHP_SESSION_NONE) session_start();
        ?>
        <a href="/2025-grp03/index.php"><img src="/2025-grp03/images/Logo.png" classe="logo"></a>
        <nav>
            <a href="/2025-grp03/php/leaderboard.php" class="linkanimation">Leaderboard</a>
            <a href="/2025-grp03/php/contact.php" class="linkanimation">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/2025-grp03/php/dashboard.php" class="linkanimation">Profile</a>
            <?php else: ?>
                <a href="/2025-grp03/php/auth.php" class="linkanimation">Sign in</a>      
             <?php endif; ?>
            <script src="/2025-grp03/script.js"></script>
        </nav>
        