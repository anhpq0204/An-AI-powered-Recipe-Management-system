<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}
$uid = intval($_SESSION['frsuid']);
$planId = intval($_GET['plan_id'] ?? 0);

// Ownership check
$stmt = $con->prepare("SELECT id, plan_name, created_at FROM meal_plans WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $planId, $uid);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$plan) {
    header('location:my-meal-plans.php');
    exit;
}

// Recipes in this plan
$rStmt = $con->prepare(
    "SELECT r.id, r.recipeTitle, r.recipePicture, r.recipePrepTime, r.totalCalories
     FROM meal_plan_recipes mpr
     JOIN recipes r ON r.id = mpr.recipe_id
     WHERE mpr.plan_id = ?
     ORDER BY mpr.id ASC"
);
$rStmt->bind_param("i", $planId);
$rStmt->execute();
$recipes = $rStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$rStmt->close();

// Aggregate ingredients
$recipeIds = array_column($recipes, 'id');
$ingredients = [];
$totalCalories = 0;
if (!empty($recipeIds)) {
    $placeholders = implode(',', array_map('intval', $recipeIds));
    $iStmt = $con->prepare(
        "SELECT i.id AS ingredient_id, i.name, i.name_vi, i.standardUnit, i.caloriesPer100g,
                SUM(ri.quantityGrams) AS total_grams
         FROM recipe_ingredients ri
         JOIN ingredients i ON ri.ingredient_id = i.id
         WHERE ri.recipe_id IN ($placeholders)
         GROUP BY i.id
         ORDER BY i.name ASC"
    );
    $iStmt->execute();
    $iResult = $iStmt->get_result();
    while ($row = $iResult->fetch_assoc()) {
        $totalGrams = floatval($row['total_grams']);
        $unit = $row['standardUnit'] ?: 'g';
        $totalCalories += ($totalGrams / 100.0) * intval($row['caloriesPer100g']);
        $ingredients[] = [
            'name'     => $row['name'],
            'name_vi'  => $row['name_vi'],
            'total_grams' => $totalGrams,
            'unit'     => $unit,
            'display'  => round($totalGrams, 1) . $unit,
        ];
    }
    $iStmt->close();
}
$totalCalories = round($totalCalories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | <?php echo htmlspecialchars($plan['plan_name']); ?></title>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">

        <div class="d-flex align-items-center gap-3 mb-1">
            <a href="my-meal-plans.php" class="btn btn-sm btn-outline-secondary"><?php _e('← My Plans'); ?></a>
        </div>

        <h1 class="user-page-title">
            <?php echo htmlspecialchars($plan['plan_name']); ?>
            <small><?php _e('Created'); ?> <?php echo date('d/m/Y H:i', strtotime($plan['created_at'])); ?> &middot; <?php echo count($recipes); ?> <?php _e('recipes'); ?></small>
        </h1>

        <div class="row g-4">
            <!-- Recipes list -->
            <div class="col-12 col-lg-5">
                <div class="user-content-card">
                    <h5 class="mb-3"><?php _e('🍽️ Recipes in this plan'); ?></h5>
                    <?php if (empty($recipes)): ?>
                        <p class="text-muted"><?php _e('No recipes in this plan.'); ?></p>
                    <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                    <?php foreach ($recipes as $r): ?>
                        <a href="../recipe-details.php?rid=<?php echo intval($r['id']); ?>"
                           class="meal-plan-recipe-card d-flex align-items-center gap-3 text-decoration-none">
                            <img src="../user/images/<?php echo htmlspecialchars($r['recipePicture']); ?>"
                                 alt="<?php echo htmlspecialchars($r['recipeTitle']); ?>"
                                 class="meal-plan-thumb">
                            <div>
                                <div class="fw-semibold"><?php echo htmlspecialchars($r['recipeTitle']); ?></div>
                                <small class="text-muted">
                                    <?php if ($r['recipePrepTime']): ?>⏱️ <?php echo intval($r['recipePrepTime']); ?> min<?php endif; ?>
                                    <?php if ($r['totalCalories'] > 0): ?> &middot; 🔥 <?php echo intval($r['totalCalories']); ?> cal<?php endif; ?>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aggregated shopping list -->
            <div class="col-12 col-lg-7">
                <div class="user-content-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><?php _e('🛒 Shopping List'); ?></h5>
                        <?php if ($totalCalories > 0): ?>
                        <span class="badge bg-warning text-dark">🔥 <?php echo $totalCalories; ?> cal total</span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($ingredients)): ?>
                        <p class="text-muted"><?php _e('No ingredient data available for these recipes.'); ?></p>
                    <?php else: ?>
                    <p class="text-muted small mb-3"><?php _e('Check off ingredients as you gather them.'); ?></p>
                    <ul class="shopping-list list-unstyled">
                    <?php foreach ($ingredients as $ing): ?>
                        <li class="shopping-list-item">
                            <label class="d-flex align-items-center gap-2 py-2" style="cursor:pointer;">
                                <input type="checkbox" class="shopping-check form-check-input mt-0" style="flex-shrink:0;">
                                <span class="shopping-label">
                                    <?php
                                    $displayName = !empty($ing['name_vi'])
                                        ? htmlspecialchars($ing['name_vi']) . ' <span class="text-muted small">/ ' . htmlspecialchars($ing['name']) . '</span>'
                                        : htmlspecialchars($ing['name']);
                                    echo $displayName;
                                    ?>
                                </span>
                                <span class="ms-auto text-muted small shopping-qty"><?php echo htmlspecialchars($ing['display']); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
<script>
document.querySelectorAll('.shopping-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var label = this.closest('label');
        if (this.checked) {
            label.style.opacity = '0.45';
            label.querySelector('.shopping-label').style.textDecoration = 'line-through';
        } else {
            label.style.opacity = '';
            label.querySelector('.shopping-label').style.textDecoration = '';
        }
    });
});
</script>
<style>
.meal-plan-thumb { width:56px; height:56px; object-fit:cover; border-radius:8px; flex-shrink:0; }
.meal-plan-recipe-card { padding:10px 12px; border-radius:10px; border:1px solid #eee; color:inherit; transition:background .15s; }
.meal-plan-recipe-card:hover { background:#f8f9fa; }
.shopping-list-item { border-bottom:1px solid #f0f0f0; }
.shopping-list-item:last-child { border-bottom:none; }
</style>
</body>
</html>
