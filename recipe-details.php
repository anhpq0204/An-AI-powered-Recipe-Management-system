<?php
require_once('includes/lang.php');
require_once('includes/helpers.php');
include('includes/dbconnection.php');
require_once('includes/session.php');
include('includes/ai-helper.php');

$frsToastMsg = '';
$frsToastType = 'success';

if(isset($_POST['submit'])) {
    $fname = trim($_POST['fname']);
    $emailid = trim($_POST['emailid']);
    $message = trim($_POST['message']);
    $recipeid = isset($_GET['rid']) ? intval($_GET['rid']) : 0;
    $stmt = $con->prepare("INSERT INTO comments(recipeId, userName, userEmail, commentMessage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $recipeid, $fname, $emailid, $message);
    if ($stmt->execute()) {
        $frsToastMsg = __('Comment added successfully. After moderation it will show');
        $frsToastType = 'success';
    } else {
        $frsToastMsg = __('Something went wrong. Please try again.');
        $frsToastType = 'danger';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Recipe details on Food Recipe System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Recipe Details</title>
    <link rel="icon" href="img/core-img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once('includes/topbar.php');?>
<?php include_once('includes/header.php');?>

<?php
$recipeid = isset($_GET['rid']) ? intval($_GET['rid']) : 0;
$isLoggedIn = !empty($_SESSION['frsuid']);
$isFav = false;
$favCount = 0;
$avgRating = 0;
$ratingCount = 0;
$userRating = 0;
if ($recipeid > 0) {
    $stmt = $con->prepare("SELECT COUNT(*) AS cnt FROM favorites WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeid);
    $stmt->execute();
    $favCount = intval($stmt->get_result()->fetch_assoc()['cnt']);
    $stmt->close();

    $stmt = $con->prepare("SELECT ROUND(AVG(rating),1) AS avg_r, COUNT(*) AS cnt FROM ratings WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeid);
    $stmt->execute();
    $rRow = $stmt->get_result()->fetch_assoc();
    $avgRating   = floatval($rRow['avg_r']);
    $ratingCount = intval($rRow['cnt']);
    $stmt->close();

    if ($isLoggedIn) {
        $uid = intval($_SESSION['frsuid']);
        $stmt = $con->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $uid, $recipeid);
        $stmt->execute();
        $isFav = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        $stmt = $con->prepare("SELECT rating FROM ratings WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $uid, $recipeid);
        $stmt->execute();
        $ur = $stmt->get_result()->fetch_assoc();
        $userRating = $ur ? intval($ur['rating']) : 0;
        $stmt->close();
    }
}

$recipeStmt = $con->prepare("SELECT r.*, u.FullName FROM recipes r LEFT JOIN users u ON u.ID = r.userId WHERE r.id = ?");
$recipeStmt->bind_param("i", $recipeid);
$recipeStmt->execute();
$recipeResult = $recipeStmt->get_result();
while ($row = $recipeResult->fetch_assoc()) {
    $ingredients = loadRecipeIngredients($con, $recipeid);
?>
    <!-- Recipe Hero Header -->
    <section class="recipe-hero-section" style="background-image: url(user/images/<?php echo htmlspecialchars($row['recipePicture']);?>);">
        <div class="recipe-hero-overlay"></div>
        <div class="container">
            <div class="recipe-hero-content">
                <span class="page-tag"><?php _e('Recipe'); ?></span>
                <h1><?php echo htmlspecialchars($row['recipeTitle']);?></h1>

                <?php if (!empty($row['FullName'])): ?>
                <p class="recipe-hero-byline">
                    <?php _e('by'); ?> <strong><?php echo htmlspecialchars($row['FullName']); ?></strong>
                    &nbsp;·&nbsp;
                    <?php echo date('d/m/Y', strtotime($row['postingDate'])); ?>
                </p>
                <?php else: ?>
                <p class="recipe-hero-byline"><?php echo date('d/m/Y', strtotime($row['postingDate'])); ?></p>
                <?php endif; ?>

                <div class="recipe-stats-row">
                    <?php if ($row['recipePrepTime']): ?>
                    <div class="recipe-stat">
                        <i class="fa fa-clock-o"></i>
                        <span class="stat-label"><?php _e('Prep'); ?></span>
                        <span class="stat-value"><?php echo intval($row['recipePrepTime']); ?> <?php _e('min'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($row['recipeCookTime']): ?>
                    <div class="recipe-stat">
                        <i class="fa fa-fire"></i>
                        <span class="stat-label"><?php _e('Cook'); ?></span>
                        <span class="stat-value"><?php echo intval($row['recipeCookTime']); ?> <?php _e('min'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($row['recipeYields']): ?>
                    <div class="recipe-stat">
                        <i class="fa fa-cutlery"></i>
                        <span class="stat-label"><?php _e('Yields'); ?></span>
                        <span class="stat-value"><?php echo htmlspecialchars($row['recipeYields']); ?> <?php _e('servings'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($row['totalCalories'] > 0): ?>
                    <div class="recipe-stat recipe-stat-cal">
                        <i class="fa fa-bolt"></i>
                        <span class="stat-label"><?php _e('Calories'); ?></span>
                        <span class="stat-value"><?php echo intval($row['totalCalories']); ?> <?php _e('cal'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="recipe-hero-actions">
                    <button class="fav-btn-detail<?php echo $isFav ? ' favorited' : ''; ?>"
                            data-recipe-id="<?php echo $recipeid; ?>"
                            data-logged-in="<?php echo $isLoggedIn ? '1' : '0'; ?>">
                        <i class="fa <?php echo $isFav ? 'fa-heart' : 'fa-heart-o'; ?> fav-icon"></i>
                        <span class="fav-label"><?php echo $isFav ? htmlspecialchars(__('Saved!')) : htmlspecialchars(__('Save to favorites')); ?></span>
                        <span class="fav-count-badge"><?php echo $favCount; ?></span>
                    </button>

                    <!-- Star Rating -->
                    <div class="hero-rating-box" data-recipe-id="<?php echo $recipeid; ?>"
                         data-logged-in="<?php echo $isLoggedIn ? '1' : '0'; ?>">
                        <?php for($s = 1; $s <= 5; $s++): ?>
                        <i class="fa fa-star hero-star<?php echo $s <= $userRating ? ' active' : ''; ?>"
                           data-value="<?php echo $s; ?>"></i>
                        <?php endfor; ?>
                        <span class="hero-rating-avg">
                            <?php if($avgRating > 0): ?>
                            <?php echo number_format($avgRating, 1); ?> <small>(<?php echo $ratingCount; ?> <?php _e('ratings'); ?>)</small>
                            <?php else: ?>
                            <small><?php _e('No ratings yet'); ?></small>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recipe Content -->
    <section class="recipe-detail-section">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-12 col-lg-8">
                    <div class="recipe-detail-card">
                        <h3><?php _e('Description'); ?></h3>
                        <div class="recipe-description">
                            <?php echo nl2br(htmlspecialchars($row['recipeDescription']));?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Ingredients -->
                <div class="col-12 col-lg-4">
                    <div class="recipe-detail-card ingredients-card">
                        <h3><?php _e('🥘 Ingredients'); ?></h3>
                        <?php if($row['totalCalories'] > 0): ?>
                        <div class="calorie-summary">
                            <span class="calorie-total">🔥 <?php echo intval($row['totalCalories']);?> <?php _e('calories'); ?></span>
                            <small class="text-muted"><?php _e('estimated total'); ?></small>
                        </div>
                        <?php endif; ?>
                        <ul class="ingredients-list">
<?php
foreach ($ingredients as $ing) {
    $lang = lang_current();
    $ingName = ($lang === 'vi' && !empty($ing['name_vi'])) ? $ing['name_vi'] : $ing['name'];
    $displayText = '';
    if (!empty($ing['quantityOriginal'])) {
        $displayText = $ing['quantityOriginal'] . ' ' . $ingName;
    } else {
        $displayText = $ingName;
    }
    // Show grams conversion if available
    $gramsNote = '';
    if ($ing['quantityGrams'] > 0) {
        $unit = $ing['standardUnit'] ?: 'g';
        $gramsNote = ' (' . round($ing['quantityGrams'], 1) . $unit . ')';
    }
?>
                            <li>
                                <label class="ingredient-check">
                                    <input type="checkbox">
                                    <span><?php echo htmlspecialchars($displayText);?><?php if($gramsNote): ?><small class="ingredient-note"><?php echo $gramsNote; ?></small><?php endif; ?></span>
                                </label>
                            </li>
<?php } ?>
                        </ul>
                    </div>
                </div>

                <!-- Similar Recipes -->
<?php
$simStmt = $con->prepare(
    "SELECT r.id, r.recipeTitle, r.recipePicture, r.totalCalories,
            COUNT(ri2.ingredient_id) AS shared
     FROM recipe_ingredients ri2
     JOIN recipes r ON r.id = ri2.recipe_id
     WHERE ri2.ingredient_id IN (
         SELECT ingredient_id FROM recipe_ingredients WHERE recipe_id = ?
     )
     AND ri2.recipe_id != ?
     AND r.status = 1
     GROUP BY r.id
     ORDER BY shared DESC
     LIMIT 4"
);
$simStmt->bind_param("ii", $recipeid, $recipeid);
$simStmt->execute();
$simResult = $simStmt->get_result();
if ($simResult->num_rows > 0):
?>
                <div class="col-12 col-lg-4">
                    <div class="recipe-detail-card similar-card">
                        <h3><?php _e('Similar Recipes'); ?></h3>
                        <div class="similar-list">
                        <?php while($sim = $simResult->fetch_assoc()): ?>
                            <a href="recipe-details.php?rid=<?php echo intval($sim['id']); ?>" class="similar-item">
                                <img src="user/images/<?php echo htmlspecialchars($sim['recipePicture']); ?>"
                                     alt="<?php echo htmlspecialchars($sim['recipeTitle']); ?>">
                                <div class="similar-info">
                                    <span class="similar-title"><?php echo htmlspecialchars($sim['recipeTitle']); ?></span>
                                    <?php if($sim['totalCalories'] > 0): ?>
                                    <span class="similar-cal">🔥 <?php echo intval($sim['totalCalories']); ?> cal</span>
                                    <?php endif; ?>
                                    <span class="similar-shared"><?php echo intval($sim['shared']); ?> <?php _e('shared ingredients'); ?></span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                        </div>
                    </div>
                </div>
<?php endif; $simStmt->close(); ?>
            </div>
<?php } ?>

            <!-- Comments Section -->
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="recipe-detail-card">
                        <h3><?php _e('💬 Leave a Comment'); ?></h3>
                        <form method="post" class="modern-form">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group-modern">
                                        <input type="text" name="fname" placeholder="<?php echo htmlspecialchars(__('Your Name')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group-modern">
                                        <input type="email" name="emailid" placeholder="<?php echo htmlspecialchars(__('Your Email')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <textarea name="message" rows="5" placeholder="<?php echo htmlspecialchars(__('Write your comment...')); ?>" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="submit" class="btn-modern btn-primary-modern"><?php _e('Post Comment'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Comments -->
<?php
$ret = mysqli_query($con, "SELECT userName, commentMessage, postingDate FROM comments WHERE status=1 AND recipeId='$recipeid'");
while ($row = mysqli_fetch_array($ret)) {
?>
                    <div class="comment-card">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($row['userName']);?></strong>
                            <span class="comment-date"><?php echo htmlspecialchars($row['postingDate']);?></span>
                        </div>
                        <p><?php echo htmlspecialchars($row['commentMessage']);?></p>
                    </div>
<?php } ?>
                </div>
            </div>
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
    <script>
    (function() {
        var ratingBox = document.querySelector('.hero-rating-box');
        if (!ratingBox) return;
        var stars = ratingBox.querySelectorAll('.hero-star');
        var recipeId = ratingBox.dataset.recipeId;
        var loggedIn = ratingBox.dataset.loggedIn === '1';
        var avgEl = ratingBox.querySelector('.hero-rating-avg');

        stars.forEach(function(star) {
            star.addEventListener('mouseover', function() {
                var val = parseInt(this.dataset.value);
                stars.forEach(function(s) {
                    s.classList.toggle('hover', parseInt(s.dataset.value) <= val);
                });
            });
            star.addEventListener('mouseleave', function() {
                stars.forEach(function(s) { s.classList.remove('hover'); });
            });
            star.addEventListener('click', function() {
                if (!loggedIn) { window.location.href = 'user/login.php'; return; }
                var val = parseInt(this.dataset.value);
                fetch('api/rate-recipe.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'recipe_id=' + recipeId + '&rating=' + val
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.success) return;
                    stars.forEach(function(s) {
                        s.classList.toggle('active', parseInt(s.dataset.value) <= data.user_rating);
                    });
                    var rounded = Math.round(data.avg_rating * 2) / 2;
                    avgEl.innerHTML = data.avg_rating.toFixed(1) +
                        ' <small>(' + data.count + ' <?php echo addslashes(__('ratings')); ?>)</small>';
                    if (window.showToast) showToast('<?php echo addslashes(__('Rating saved!')); ?>', 'success');
                });
            });
        });
    })();
    </script>
</body>
</html>
