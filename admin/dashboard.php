<?php require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
  header('location:logout.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Admin Dashboard</title>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">

        <h1 class="admin-page-title">
            <?php _e('Dashboard'); ?>
            <small><?php _e('Welcome back,'); ?> <?php
                $adid = $_SESSION['frsaid'];
                $ret = mysqli_query($con,"SELECT AdminName FROM admins WHERE ID='$adid'");
                $row = mysqli_fetch_array($ret);
                echo htmlspecialchars($row['AdminName'] ?? 'Admin');
            ?></small>
        </h1>

        <!-- Stats Grid -->
        <div class="admin-stats-grid">

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM users");
            $r = mysqli_fetch_array($q);
            $usercounts = $r['cnt'];
            ?>
            <a href="reg-users.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-users"><i class="fa fa-users"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Registered Users'); ?></h4>
                    <h2><?php echo $usercounts;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM recipes");
            $r = mysqli_fetch_array($q);
            $totallistedfood = $r['cnt'];
            ?>
            <a href="listed-recipes.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-recipes"><i class="fa fa-cutlery"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Listed Recipes'); ?></h4>
                    <h2><?php echo $totallistedfood;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM comments");
            $r = mysqli_fetch_array($q);
            $allcomments = $r['cnt'];
            ?>
            <a href="all-comments.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-comments"><i class="fa fa-comments"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('All Comments'); ?></h4>
                    <h2><?php echo $allcomments;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM comments WHERE status IS NULL");
            $r = mysqli_fetch_array($q);
            $newcomments = $r['cnt'];
            ?>
            <a href="new-comments.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-new"><i class="fa fa-clock-o"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('New Comments'); ?></h4>
                    <h2><?php echo $newcomments;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM comments WHERE status='0'");
            $r = mysqli_fetch_array($q);
            $rejected = $r['cnt'];
            ?>
            <a href="rejected-comments.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-rejected"><i class="fa fa-times-circle"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Rejected Comments'); ?></h4>
                    <h2><?php echo $rejected;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM comments WHERE status='1'");
            $r = mysqli_fetch_array($q);
            $approved = $r['cnt'];
            ?>
            <a href="approved-comments.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-approved"><i class="fa fa-check-circle"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Approved Comments'); ?></h4>
                    <h2><?php echo $approved;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM enquiries WHERE adminRemark IS NULL");
            $r = mysqli_fetch_array($q);
            $unreadenq = $r['cnt'];
            ?>
            <a href="unreadenq.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-unread"><i class="fa fa-envelope"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Unread Enquiry'); ?></h4>
                    <h2><?php echo $unreadenq;?></h2>
                </div>
            </a>

            <?php
            $q = mysqli_query($con,"SELECT COUNT(*) as cnt FROM enquiries WHERE adminRemark IS NOT NULL");
            $r = mysqli_fetch_array($q);
            $readenq = $r['cnt'];
            ?>
            <a href="readenq.php" class="admin-stat-card">
                <div class="admin-stat-icon bg-read"><i class="fa fa-envelope-open"></i></div>
                <div class="admin-stat-info">
                    <h4><?php _e('Read Enquiry'); ?></h4>
                    <h2><?php echo $readenq;?></h2>
                </div>
            </a>

        </div>

    </section>
    <!-- footer -->
    <?php include_once('includes/footer.php');?>
</section>
</section>

<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
