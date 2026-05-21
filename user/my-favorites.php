<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}
$uid = intval($_SESSION['frsuid']);

$stmt = $con->prepare(
    "SELECT r.id, r.recipeTitle, r.recipePicture, r.recipePrepTime, r.recipeYields, r.totalCalories,
            u.FullName, f.created_at AS saved_at
     FROM favorites f
     JOIN recipes r ON r.id = f.recipe_id
     LEFT JOIN users u ON u.ID = r.userId
     WHERE f.user_id = ?
     ORDER BY f.created_at DESC"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | <?php _e('My Favorites'); ?></title>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">

        <h1 class="user-page-title">
            ❤️ <?php _e('My Favorites'); ?>
            <small><?php _e('Your favorite recipes'); ?></small>
        </h1>

        <div class="user-content-card">
            <?php if (empty($favorites)): ?>
            <div class="empty-state text-center py-5">
                <div style="font-size:48px;">🤍</div>
                <h4><?php _e('No favorites yet'); ?></h4>
                <p class="text-muted"><?php _e('Browse recipes and click the heart to save your favorites.'); ?></p>
                <a href="../recipes.php" class="btn btn-primary mt-2"><?php _e('Go to Recipes'); ?></a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th><?php _e('Recipe'); ?></th>
                            <th><?php _e('Prep Time'); ?></th>
                            <th><?php _e('Yields'); ?></th>
                            <th><?php _e('Saved'); ?></th>
                            <th><?php _e('Action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($favorites as $i => $r): ?>
                        <tr id="fav-row-<?php echo intval($r['id']); ?>">
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="../user/images/<?php echo htmlspecialchars($r['recipePicture']); ?>"
                                         alt="" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($r['recipeTitle']); ?></strong>
                                        <?php if ($r['FullName']): ?>
                                        <div class="text-muted small"><?php _e('by'); ?> <?php echo htmlspecialchars($r['FullName']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $r['recipePrepTime'] ? intval($r['recipePrepTime']) . ' ' . __('min') : '—'; ?></td>
                            <td><?php echo $r['recipeYields'] ? htmlspecialchars($r['recipeYields']) . ' ' . __('servings') : '—'; ?></td>
                            <td class="text-muted small"><?php echo date('d/m/Y', strtotime($r['saved_at'])); ?></td>
                            <td>
                                <a href="../recipe-details.php?rid=<?php echo intval($r['id']); ?>"
                                   class="btn btn-sm btn-outline-primary me-1"><?php _e('View'); ?></a>
                                <button class="btn btn-sm btn-outline-danger fav-remove-btn"
                                        data-recipe-id="<?php echo intval($r['id']); ?>">
                                    🗑 <?php _e('Remove'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </section>
</section>

<?php include_once('includes/footer.php');?>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
<script>
document.querySelectorAll('.fav-remove-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var recipeId = this.dataset.recipeId;
        var row = document.getElementById('fav-row-' + recipeId);
        btn.disabled = true;
        fetch('../api/toggle-favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'recipe_id=' + encodeURIComponent(recipeId)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && !data.is_favorited && row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(function() { row.remove(); }, 300);
            } else {
                btn.disabled = false;
            }
        })
        .catch(function() { btn.disabled = false; });
    });
});
</script>
</body>
</html>
