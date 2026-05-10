<?php
require_once __DIR__ . '/lang.php';
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 604800);
    ini_set('session.cookie_lifetime', 604800);
    session_start();
}
if (!isset($con)) {
    include_once __DIR__ . '/dbconnection.php';
}
$_loggedInUser = null;
if (!empty($_SESSION['frsuid'])) {
    $__uid = intval($_SESSION['frsuid']);
    $__stmt = $con->prepare("SELECT FullName FROM users WHERE ID = ? LIMIT 1");
    if ($__stmt) {
        $__stmt->bind_param("i", $__uid);
        $__stmt->execute();
        $__stmt->bind_result($__fullName);
        if ($__stmt->fetch()) {
            $_loggedInUser = htmlspecialchars($__fullName);
        }
        $__stmt->close();
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
                                <li class="<?php if($currentPage == 'index.php') echo 'active';?>"><a href="index.php"><?php _e('Home'); ?></a></li>
                                <li class="<?php if($currentPage == 'about.php') echo 'active';?>"><a href="about.php"><?php _e('About Us'); ?></a></li>
                                <li class="<?php if(in_array($currentPage, ['recipes.php', 'recipe-details.php', 'search.php'])) echo 'active';?>"><a href="recipes.php"><?php _e('Recipes'); ?></a></li>
                                <li><a href="admin/login.php"><?php _e('Admin'); ?></a></li>
                                <li class="<?php if($currentPage == 'contact.php') echo 'active';?>"><a href="contact.php"><?php _e('Contact'); ?></a></li>
                            </ul>

                            <div class="search-btn">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </div>

                            <!-- Language switcher -->
                            <?php $__lang = lang_current(); ?>
                            <div class="lang-switcher">
                                <a href="<?php echo lang_switcher_url('vi'); ?>" class="<?php echo $__lang === 'vi' ? 'active' : ''; ?>">VI</a>
                                <span class="lang-sep">|</span>
                                <a href="<?php echo lang_switcher_url('en'); ?>" class="<?php echo $__lang === 'en' ? 'active' : ''; ?>">EN</a>
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
                                    <a href="user/dashboard.php" class="user-dropdown-item"><i class="fa fa-dashboard"></i> Dashboard</a>
                                    <a href="user/meal-planner.php" class="user-dropdown-item"><i class="fa fa-calendar"></i> Meal Planner</a>
                                    <a href="user/my-meal-plans.php" class="user-dropdown-item"><i class="fa fa-list"></i> My Plans</a>
                                    <a href="user/profile.php" class="user-dropdown-item"><i class="fa fa-user"></i> Profile</a>
                                    <div class="user-dropdown-divider"></div>
                                    <a href="logout.php" class="user-dropdown-item user-dropdown-logout"><i class="fa fa-sign-out"></i> Logout</a>
                                </div>
                            </div>
                            <?php else: ?>
                            <a href="user/login.php" class="nav-login-btn">
                                <i class="fa fa-user-circle"></i> <?php _e('Login'); ?>
                            </a>
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