<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = intval($_SESSION['frsuid']);

// User name + owned-recipe count in one round trip.
$stmt = $con->prepare("SELECT
        (SELECT FullName FROM users WHERE ID = ?) AS full_name,
        (SELECT COUNT(*) FROM recipes WHERE userId = ?) AS recipe_count");
$stmt->bind_param("ii", $uid, $uid);
$stmt->execute();
$meta = $stmt->get_result()->fetch_assoc();
$stmt->close();
$fullName = $meta['full_name'] ?? 'User';
$fcounts  = intval($meta['recipe_count']);

// All four comment buckets in a single aggregated query (was 4 queries).
$stmt = $con->prepare("SELECT
        COUNT(*) AS total,
        SUM(c.status IS NULL) AS new_cnt,
        SUM(c.status = 0)     AS rejected_cnt,
        SUM(c.status = 1)     AS approved_cnt
    FROM comments c JOIN recipes r ON r.id = c.recipeId
    WHERE r.userId = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$cstats = $stmt->get_result()->fetch_assoc();
$stmt->close();
$allcomments = intval($cstats['total']);
$newcomments = intval($cstats['new_cnt']);
$rejected    = intval($cstats['rejected_cnt']);
$approved    = intval($cstats['approved_cnt']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | User Dashboard</title>
</head>
<body>
<section id="container">

<!--header start-->
<?php include_once('includes/header.php');?>
<!--sidebar start-->
<?php include_once('includes/sidebar.php');?>

<!--main content start-->
<section id="main-content">
	<section class="wrapper">

        <h1 class="user-page-title">
            <?php _e('User Dashboard'); ?>
            <small><?php _e('Welcome back,'); ?> <?php echo htmlspecialchars($fullName); ?></small>
        </h1>

		<!-- Stats Grid -->
		<div class="user-stats-grid">

            <a href="manage-recipes.php" class="user-stat-card">
                <div class="user-stat-icon bg-recipes"><i class="fa fa-cutlery"></i></div>
                <div class="user-stat-info">
                    <h4><?php _e('Total Food Recipes'); ?></h4>
                    <h2><?php echo $fcounts;?></h2>
                </div>
            </a>

            <a href="all-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-all-comments"><i class="fa fa-comments"></i></div>
                <div class="user-stat-info">
                    <h4><?php _e('All Comments'); ?></h4>
                    <h2><?php echo $allcomments;?></h2>
                </div>
            </a>

            <a href="new-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-new-comments"><i class="fa fa-clock-o"></i></div>
                <div class="user-stat-info">
                    <h4><?php _e('New Comments'); ?></h4>
                    <h2><?php echo $newcomments;?></h2>
                </div>
            </a>

            <a href="rejected-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-rejected"><i class="fa fa-times-circle"></i></div>
                <div class="user-stat-info">
                    <h4><?php _e('Rejected Comments'); ?></h4>
                    <h2><?php echo $rejected;?></h2>
                </div>
            </a>

            <a href="approved-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-approved"><i class="fa fa-check-circle"></i></div>
                <div class="user-stat-info">
                    <h4><?php _e('Approved Comments'); ?></h4>
                    <h2><?php echo $approved;?></h2>
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
