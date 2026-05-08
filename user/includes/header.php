<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once(__DIR__ . '/../../includes/dbconnection.php');
?>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="../dashboard-assets/css/font-awesome.css" rel="stylesheet">
<!-- Legacy style (keep for sidebar JS + base resets) -->
<link href="../dashboard-assets/css/style.css" rel="stylesheet">
<!-- Modern User Override -->
<link href="css/user-modern.css" rel="stylesheet">

<header class="header fixed-top clearfix">
    <!-- Brand -->
    <div class="brand">
        <a href="dashboard.php" class="logo">FRS User</a>
        <div class="sidebar-toggle-box">
            <div class="fa fa-bars"></div>
        </div>
    </div>
    <!-- Spacer -->
    <div class="nav notify-row" id="top_menu"></div>
    <!-- Right nav -->
    <div class="top-nav clearfix">
        <ul class="nav float-end top-menu">
            <li class="dropdown">
                <a data-bs-toggle="dropdown" class="dropdown-toggle" href="#">
<?php
$uid = $_SESSION['frsuid'] ?? '';
$name = 'User';
if ($uid) {
    $header_ret=mysqli_query($con,"SELECT FullName FROM users WHERE ID='$uid'");
    if ($header_row=mysqli_fetch_array($header_ret)) {
        $name = $header_row['FullName'];
    }
}
?>
                    <img alt="" src="images/2.png">
                    <span class="username"><?php echo htmlspecialchars($name); ?></span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
                    <li><a href="profile.php"><i class="fa fa-user"></i> Profile</a></li>
                    <li><a href="change-password.php"><i class="fa fa-lock"></i> Change Password</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Log Out</a></li>
                </ul>
            </li>
        </ul>
    </div>
</header>