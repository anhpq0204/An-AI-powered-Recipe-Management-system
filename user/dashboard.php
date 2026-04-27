<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}
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
            User Dashboard
            <small>Welcome back, <?php
                $uid = $_SESSION['frsuid'];
                $ret = mysqli_query($con,"SELECT FullName FROM users WHERE ID='$uid'");
                $row = mysqli_fetch_array($ret);
                echo htmlspecialchars($row['FullName'] ?? 'User');
            ?></small>
        </h1>

		<!-- Stats Grid -->
		<div class="user-stats-grid">
			
            <?php 
            $query=mysqli_query($con,"SELECT id FROM recipes WHERE userId='$uid'");
            $fcounts=mysqli_num_rows($query);
            ?>
            <a href="manage-recipes.php" class="user-stat-card">
                <div class="user-stat-icon bg-recipes"><i class="fa fa-cutlery"></i></div>
                <div class="user-stat-info">
                    <h4>Total Food Recipes</h4>
                    <h2><?php echo $fcounts;?></h2>
                </div>
            </a>

            <?php 
            $query=mysqli_query($con,"SELECT comments.id FROM comments JOIN recipes ON recipes.id=comments.recipeId WHERE recipes.userId='$uid'");
            $allcomments=mysqli_num_rows($query);
            ?>
            <a href="all-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-all-comments"><i class="fa fa-comments"></i></div>
                <div class="user-stat-info">
                    <h4>All Comments</h4>
                    <h2><?php echo $allcomments;?></h2>
                </div>
            </a>

            <?php 
            $query=mysqli_query($con,"SELECT comments.id FROM comments JOIN recipes ON recipes.id=comments.recipeId WHERE recipes.userId='$uid' AND comments.status IS NULL");
            $newcomments=mysqli_num_rows($query);
            ?>
            <a href="new-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-new-comments"><i class="fa fa-clock-o"></i></div>
                <div class="user-stat-info">
                    <h4>New Comments</h4>
                    <h2><?php echo $newcomments;?></h2>
                </div>
            </a>

            <?php 
            $query1=mysqli_query($con,"SELECT comments.id FROM comments JOIN recipes ON recipes.id=comments.recipeId WHERE recipes.userId='$uid' AND comments.status='0'");
            $rejected=mysqli_num_rows($query1);
            ?>
            <a href="rejected-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-rejected"><i class="fa fa-times-circle"></i></div>
                <div class="user-stat-info">
                    <h4>Rejected Comments</h4>
                    <h2><?php echo $rejected;?></h2>
                </div>
            </a>

            <?php 
            $query2=mysqli_query($con,"SELECT comments.id FROM comments JOIN recipes ON recipes.id=comments.recipeId WHERE recipes.userId='$uid' AND comments.status='1'");
            $approved=mysqli_num_rows($query2);
            ?>
            <a href="approved-comments.php" class="user-stat-card">
                <div class="user-stat-icon bg-approved"><i class="fa fa-check-circle"></i></div>
                <div class="user-stat-info">
                    <h4>Approved Comments</h4>
                    <h2><?php echo $approved;?></h2>
                </div>
            </a>

		</div>

	</section>
    <!-- footer -->
	<?php include_once('includes/footer.php');?>	  
</section>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
