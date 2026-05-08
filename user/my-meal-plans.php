<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}
$uid = intval($_SESSION['frsuid']);

$stmt = $con->prepare(
    "SELECT mp.id, mp.plan_name, mp.created_at, COUNT(mpr.id) AS recipe_count
     FROM meal_plans mp
     LEFT JOIN meal_plan_recipes mpr ON mpr.plan_id = mp.id
     WHERE mp.user_id = ?
     GROUP BY mp.id
     ORDER BY mp.created_at DESC"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | My Meal Plans</title>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">

        <h1 class="user-page-title">
            My Meal Plans
            <small>Your saved ingredient shopping lists</small>
        </h1>

        <div class="user-content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Saved Plans</h4>
                <a href="meal-planner.php" class="btn btn-sm btn-primary">+ New Plan</a>
            </div>

            <?php if (empty($plans)): ?>
            <div class="empty-state text-center py-5">
                <div style="font-size:48px;">📋</div>
                <h4>No meal plans yet</h4>
                <p class="text-muted">Go to Meal Planner to select recipes and save your first plan.</p>
                <a href="meal-planner.php" class="btn btn-primary mt-2">Go to Meal Planner</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Plan Name</th>
                            <th>Created</th>
                            <th>Recipes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($plans as $i => $plan): ?>
                        <tr id="plan-row-<?php echo intval($plan['id']); ?>">
                            <td><?php echo $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($plan['plan_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($plan['created_at']))); ?></td>
                            <td><?php echo intval($plan['recipe_count']); ?> recipes</td>
                            <td>
                                <a href="view-meal-plan.php?plan_id=<?php echo intval($plan['id']); ?>"
                                   class="btn btn-sm btn-outline-primary me-1">👁 View</a>
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="deletePlan(<?php echo intval($plan['id']); ?>, this)">🗑 Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </section>
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
<script>
function deletePlan(planId, btn) {
    if (!confirm('Delete this meal plan?')) return;
    btn.disabled = true;

    var form = new FormData();
    form.append('plan_id', planId);

    fetch('../api/delete-meal-plan.php', { method: 'POST', body: form })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var row = document.getElementById('plan-row-' + planId);
            if (row) row.remove();
        } else {
            alert('Could not delete plan: ' + (data.error || 'Unknown error'));
            btn.disabled = false;
        }
    })
    .catch(function() {
        alert('Error deleting plan. Please try again.');
        btn.disabled = false;
    });
}
</script>
</body>
</html>
