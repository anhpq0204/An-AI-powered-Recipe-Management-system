<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_loggedInUser = null;
if (!empty($_SESSION['frsuid'])) {
    $__uid = intval($_SESSION['frsuid']);
    $__ret = mysqli_query($con, "SELECT FullName FROM users WHERE ID='$__uid' LIMIT 1");
    if ($__ret && $__row = mysqli_fetch_assoc($__ret)) {
        $_loggedInUser = htmlspecialchars($__row['FullName']);
    }
}
$_avatarInitial = $_loggedInUser ? mb_strtoupper(mb_substr($_loggedInUser, 0, 1)) : '';
?>
<header class="header-area">
    <!-- Navbar -->
    <div class="delicious-main-menu">
        <div class="classy-nav-container breakpoint-off">
            <div class="container">
                <nav class="classy-navbar justify-content-between" id="deliciousNav">
                    <a class="nav-brand" href="index.php">FOOD Recipes</a>

                    <div class="classy-navbar-toggler">
                        <span class="navbarToggler"><span></span><span></span><span></span></span>
                    </div>

                    <div class="classy-menu">
                        <div class="classycloseIcon">
                            <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                        </div>

                        <div class="classynav">
                            <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                            <ul>
                                <li class="<?php if($currentPage == 'index.php') echo 'active';?>"><a href="index.php">Home</a></li>
                                <li class="<?php if($currentPage == 'about.php') echo 'active';?>"><a href="about.php">About Us</a></li>
                                <li class="<?php if(in_array($currentPage, ['recipes.php', 'recipe-details.php', 'search.php'])) echo 'active';?>"><a href="recipes.php">Recipes</a></li>
                                <?php if (!$_loggedInUser): ?>
                                <li><a href="user/login.php">Login</a></li>
                                <?php endif; ?>
                                <li><a href="admin/login.php">Admin</a></li>
                                <li class="<?php if($currentPage == 'contact.php') echo 'active';?>"><a href="contact.php">Contact</a></li>
                            </ul>

                            <div class="search-btn">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </div>

                            <?php if ($_loggedInUser): ?>
                            <div class="user-avatar-wrap">
                                <button class="user-avatar-btn" id="userAvatarBtn" aria-label="User menu">
                                    <span class="user-avatar-initial"><?php echo $_avatarInitial; ?></span>
                                </button>
                                <div class="user-dropdown" id="userDropdown">
                                    <div class="user-dropdown-header">
                                        <div class="user-dropdown-avatar"><?php echo $_avatarInitial; ?></div>
                                        <div>
                                            <div class="user-dropdown-name"><?php echo $_loggedInUser; ?></div>
                                        </div>
                                    </div>
                                    <div class="user-dropdown-divider"></div>
                                    <a href="user/dashboard.php" class="user-dropdown-item">
                                        <i class="fa fa-dashboard"></i> Dashboard
                                    </a>
                                    <a href="user/meal-planner.php" class="user-dropdown-item">
                                        <i class="fa fa-calendar"></i> Meal Planner
                                    </a>
                                    <a href="user/my-meal-plans.php" class="user-dropdown-item">
                                        <i class="fa fa-list"></i> My Plans
                                    </a>
                                    <a href="user/profile.php" class="user-dropdown-item">
                                        <i class="fa fa-user"></i> Profile
                                    </a>
                                    <div class="user-dropdown-divider"></div>
                                    <a href="logout.php" class="user-dropdown-item user-dropdown-logout">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
<script>
(function() {
    var btn = document.getElementById('userAvatarBtn');
    var dropdown = document.getElementById('userDropdown');
    if (!btn) return;
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });
    document.addEventListener('click', function() {
        if (dropdown) dropdown.classList.remove('show');
    });
})();
</script>