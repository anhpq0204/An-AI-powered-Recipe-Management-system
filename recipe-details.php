<?php include('includes/dbconnection.php');
include('includes/ai-helper.php');
require_once('includes/lang.php');

if(isset($_POST['submit'])) {
    $fname = trim($_POST['fname']);
    $emailid = trim($_POST['emailid']);
    $message = trim($_POST['message']);
    $recipeid = isset($_GET['rid']) ? intval($_GET['rid']) : 0;
    $stmt = $con->prepare("INSERT INTO comments(recipeId, userName, userEmail, commentMessage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $recipeid, $fname, $emailid, $message);
    if ($stmt->execute()) {
        echo "<script>alert('" . addslashes(__('Comment added successfully. After moderation it will show')) . "');</script>";
    } else {
        echo "<script>alert('" . addslashes(__('Something went wrong. Please try again.')) . "');</script>";
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
$ret = mysqli_query($con, "SELECT * FROM recipes WHERE id='$recipeid'");
while ($row = mysqli_fetch_array($ret)) {
    // Load ingredients từ bảng mới
    $ingredients = loadRecipeIngredients($con, $recipeid);
?>
    <!-- Page Header with Recipe Image -->
    <section class="page-header-section page-header-tall" style="background-image: url(user/images/<?php echo htmlspecialchars($row['recipePicture']);?>);">
        <div class="page-header-overlay"></div>
        <div class="container">
            <div class="page-header-content">
                <span class="page-tag"><?php _e('Recipe'); ?></span>
                <h1><?php echo htmlspecialchars($row['recipeTitle']);?></h1>
                <div class="recipe-detail-meta">
                    <span>📅 <?php echo htmlspecialchars($row['postingDate']);?></span>
                    <?php if($row['recipePrepTime']) { ?><span>⏱️ <?php _e('Prep'); ?>: <?php echo htmlspecialchars($row['recipePrepTime']);?> <?php _e('min'); ?></span><?php } ?>
                    <?php if($row['recipeCookTime']) { ?><span>🍳 <?php _e('Cook'); ?>: <?php echo htmlspecialchars($row['recipeCookTime']);?> <?php _e('min'); ?></span><?php } ?>
                    <?php if($row['recipeYields']) { ?><span>🍽️ <?php _e('Yields'); ?>: <?php echo htmlspecialchars($row['recipeYields']);?> <?php _e('servings'); ?></span><?php } ?>
                    <?php if($row['totalCalories'] > 0) { ?>
                    <span class="calorie-badge">🔥 <?php echo intval($row['totalCalories']);?> <?php _e('cal'); ?></span>
                    <?php } ?>
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
                                    <span><?php echo htmlspecialchars($displayText);?><?php if($gramsNote): ?><small class="text-muted"><?php echo $gramsNote; ?></small><?php endif; ?></span>
                                </label>
                            </li>
<?php } ?>
                        </ul>
                    </div>
                </div>
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
    <script src="js/app.js"></script>
</body>
</html>
