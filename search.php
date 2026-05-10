<?php include('includes/dbconnection.php'); require_once('includes/lang.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Search recipes on Food Recipe System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Search Results</title>
    <link rel="icon" href="img/core-img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once('includes/topbar.php');?>
<?php include_once('includes/header.php');?>

<?php $search = isset($_POST['search']) ? $_POST['search'] : '';?>

    <!-- Page Header -->
    <section class="page-header-section" style="background-image: url(img/bg-img/breadcumb5.jpg);">
        <div class="page-header-overlay"></div>
        <div class="container">
            <div class="page-header-content">
                <span class="page-tag"><?php _e('Search'); ?></span>
                <h1><?php _e('Results for'); ?> "<?php echo htmlspecialchars($search);?>"</h1>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="featured-recipes-section">
        <div class="container">
<?php
$ret = mysqli_query($con, "SELECT recipeTitle, recipePicture, id, postingDate, totalCalories FROM recipes WHERE recipeTitle LIKE '%$search%'");
$count = mysqli_num_rows($ret);
if ($count > 0) {
?>
            <div class="modern-section-heading">
                <p><?php echo $count; ?> <?php echo $count > 1 ? __('recipes found') : __('recipe found'); ?></p>
            </div>
            <div class="recipes-grid">
<?php while ($row = mysqli_fetch_array($ret)) { ?>
                <div class="recipe-card animate-in">
                    <div class="recipe-card-image">
                        <img src="user/images/<?php echo htmlspecialchars($row['recipePicture']);?>" alt="<?php echo htmlspecialchars($row['recipeTitle']);?>" loading="lazy">
                        <div class="recipe-card-overlay">
                            <a href="recipe-details.php?rid=<?php echo intval($row['id']);?>" class="view-recipe-btn"><?php _e('View Recipe'); ?></a>
                        </div>
                    </div>
                    <div class="recipe-card-body">
                        <h5><a href="recipe-details.php?rid=<?php echo intval($row['id']);?>"><?php echo htmlspecialchars($row['recipeTitle']);?></a></h5>
                        <div class="recipe-meta">
                            <span><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($row['postingDate']);?></span>
                            <?php if($row['totalCalories'] > 0) { ?>
                            <span class="calorie-badge">🔥 <?php echo $row['totalCalories'];?> cal</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
<?php } ?>
            </div>
<?php } else { ?>
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <h3><?php _e('No recipes found'); ?></h3>
                <p><?php _e('We couldn\'t find any recipes matching'); ?> "<strong><?php echo htmlspecialchars($search);?></strong>"</p>
                <a href="recipes.php" class="btn-modern btn-primary-modern" style="margin-top:20px;"><?php _e('Browse All Recipes'); ?></a>
            </div>
<?php } ?>
        </div>
    </section>

<?php include_once('includes/footer.php');?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
