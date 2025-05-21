<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<a href="/2025-grp03/index.php"><img src="/2025-grp03/images/Logo.png" class="logo" ></a>
<nav>
    <a href="/2025-grp03/php/leaderboard.php" class="linkanimation">Leaderboard</a>
    <a href="/2025-grp03/php/contact.php" class="linkanimation">Contact</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/2025-grp03/php/dashboard.php" class="linkanimation">Profile</a>
    <?php else: ?>
        <a href="/2025-grp03/php/auth.php" class="linkanimation">Sign in</a>      
    <?php endif; ?>
</nav>
<input type="checkbox" id="menu_toggle" class="menu_toggle" style="display:none;">
<label for="menu_toggle" class="menu-burger">
    <img src="/2025-grp03/icons/menu.png" alt="Menu" />
</label>
<div class="dropdown_menu">
    <a class="linkanimation" href="/2025-grp03/php/leaderboard.php">Leaderboard</a>
    <a class="linkanimation" href="/2025-grp03/php/contact.php">Contact</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="linkanimation" href="/2025-grp03/php/dashboard.php">Profile</a>
    <?php else: ?>
        <a class="linkanimation" href="/2025-grp03/php/auth.php">Sign in</a>
    <?php endif; ?>
</div>
<script src="/2025-grp03/script.js"></script>