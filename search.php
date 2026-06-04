<?php
require_once('includes/lang.php');
require_once('includes/helpers.php');
include('includes/dbconnection.php');
require_once('includes/session.php');

$search = trim($_POST['search'] ?? $_GET['q'] ?? '');

$userFavIds = [];
$isLoggedIn = !empty($_SESSION['frsuid']);
if ($isLoggedIn) {
    $uid = intval($_SESSION['frsuid']);
    $favStmt = $con->prepare("SELECT recipe_id FROM favorites WHERE user_id = ?");
    $favStmt->bind_param("i", $uid);
    $favStmt->execute();
    foreach ($favStmt->get_result()->fetch_all(MYSQLI_ASSOC) as $fr) {
        $userFavIds[$fr['recipe_id']] = true;
    }
    $favStmt->close();
}
?>
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
$rows = [];
$count = 0;

if ($search !== '') {
    // Full-text search with relevance score, fallback to LIKE
    $stmt = $con->prepare(
        "SELECT r.id, r.recipeTitle, r.recipePicture, r.postingDate, r.totalCalories,
                r.recipePrepTime, r.recipeYields, u.FullName,
                (SELECT COUNT(*) FROM favorites WHERE recipe_id = r.id) AS fav_count,
                (SELECT ROUND(AVG(rating),1) FROM ratings WHERE recipe_id = r.id) AS avg_rating,
                (SELECT COUNT(*) FROM ratings WHERE recipe_id = r.id) AS rating_count,
                MATCH(r.recipeTitle, r.recipeDescription) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance
         FROM recipes r
         LEFT JOIN users u ON u.ID = r.userId
         WHERE r.status = 1
           AND (
               MATCH(r.recipeTitle, r.recipeDescription) AGAINST(? IN NATURAL LANGUAGE MODE)
               OR r.recipeTitle LIKE ?
           )
         ORDER BY relevance DESC, r.id DESC
         LIMIT 50"
    );
    $like = '%' . $search . '%';
    $stmt->bind_param("sss", $search, $search, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $count = count($rows);
    $stmt->close();
}

if ($count > 0):
?>
            <div class="modern-section-heading" style="margin-bottom:30px;">
                <p><?php echo $count; ?> <?php echo $count > 1 ? __('recipes found') : __('recipe found'); ?></p>
            </div>
            <div class="recipes-grid">
<?php foreach ($rows as $row):
    $isFav = isset($userFavIds[intval($row['id'])]);
    $favCount = intval($row['fav_count']);
    $avgRating = floatval($row['avg_rating']);
    $ratingCount = intval($row['rating_count']);
?>
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
                            <?php if($row['recipePrepTime']): ?>
                            <span><i class="fa fa-clock-o"></i> <?php echo intval($row['recipePrepTime']); ?> <?php _e('min'); ?></span>
                            <?php endif; ?>
                            <?php if($row['totalCalories'] > 0): ?>
                            <span class="calorie-badge">🔥 <?php echo intval($row['totalCalories']); ?> cal</span>
                            <?php endif; ?>
                        </div>
                        <?php render_stars($avgRating, $ratingCount); ?>
                        <?php if($row['FullName']): ?>
                        <div class="recipe-author"><?php _e('by'); ?> <strong><?php echo htmlspecialchars($row['FullName']); ?></strong></div>
                        <?php endif; ?>
                        <button class="fav-btn<?php echo $isFav ? ' favorited' : ''; ?>"
                                data-recipe-id="<?php echo intval($row['id']); ?>"
                                data-logged-in="<?php echo $isLoggedIn ? '1' : '0'; ?>"
                                title="<?php echo $isFav ? htmlspecialchars(__('Remove from favorites')) : htmlspecialchars(__('Save to favorites')); ?>">
                            <span class="fav-count"><?php echo $favCount; ?></span>
                            <i class="fa <?php echo $isFav ? 'fa-heart' : 'fa-heart-o'; ?> fav-icon"></i>
                        </button>
                    </div>
                </div>
<?php endforeach; ?>
            </div>
<?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <h3><?php _e('No recipes found'); ?></h3>
                <p><?php _e('We couldn\'t find any recipes matching'); ?> "<strong><?php echo htmlspecialchars($search);?></strong>"</p>
                <a href="recipes.php" class="btn-modern btn-primary-modern" style="margin-top:20px;"><?php _e('Browse All Recipes'); ?></a>
            </div>
<?php endif; ?>
        </div>
    </section>

<?php include_once('includes/footer.php');?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window._favI18n = {
        save:   '<?php echo addslashes(__('Save to favorites')); ?>',
        saved:  '<?php echo addslashes(__('Saved!')); ?>',
        remove: '<?php echo addslashes(__('Remove from favorites')); ?>'
    };
    </script>
    <script src="js/app.js"></script>
</body>
</html>
