<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php'); exit;
}
$uid = intval($_SESSION['frsuid']);

// Mark all as read
$con->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$uid]);

$stmt = $con->prepare(
    "SELECT n.*, r.recipeTitle FROM notifications n
     LEFT JOIN recipes r ON r.id = n.recipe_id
     WHERE n.user_id = ?
     ORDER BY n.created_at DESC LIMIT 50"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$notifs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('Notifications'); ?></title>
<link rel="stylesheet" href="../dashboard-assets/css/style.css">
<link rel="stylesheet" href="../dashboard-assets/css/font-awesome.css">
<link rel="stylesheet" href="css/user-modern.css">
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span><?php _e('Notifications'); ?></span>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($notifs)): ?>
                        <div class="text-center p-5 text-muted">
                            <i class="fa fa-bell-o fa-3x mb-3"></i>
                            <p><?php _e('No notifications yet'); ?></p>
                        </div>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                        <?php foreach($notifs as $n):
                            $icon = $n['type'] === 'recipe_approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                        ?>
                            <li class="list-group-item<?php echo $n['is_read'] ? '' : ' fw-bold bg-light'; ?>">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fa <?php echo $icon; ?> fa-lg mt-1"></i>
                                    <div class="flex-grow-1">
                                        <p class="mb-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                        <?php if($n['recipe_id']): ?>
                                        <a href="../recipe-details.php?rid=<?php echo intval($n['recipe_id']); ?>" class="btn btn-sm btn-outline-primary mt-1">
                                            <?php _e('View Recipe'); ?>
                                        </a>
                                        <?php endif; ?>
                                        <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($n['created_at']); ?></small>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include_once('includes/footer.php');?>
</section>
</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
