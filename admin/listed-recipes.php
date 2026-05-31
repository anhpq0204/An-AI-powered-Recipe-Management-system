<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Code for deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $rid = isset($_GET['bsid']) ? intval($_GET['bsid']) : 0;
    $query = mysqli_query($con, "DELETE FROM recipes WHERE id='$rid'");
    if ($query) {
        $_SESSION['frs_toast_msg'] = __('Recipe deleted successfully.');
        $_SESSION['frs_toast_type'] = 'success';
        header('Location: listed-recipes.php');
        exit;
    } else {
        $frsToastMsg = __('Something went wrong. Please try again.');
        $frsToastType = 'danger';
    }
}

// Approve recipe
if (isset($_GET['action']) && $_GET['action'] == 'approve') {
    $rid = isset($_GET['bsid']) ? intval($_GET['bsid']) : 0;
    $stmt = $con->prepare("UPDATE recipes SET status = 1 WHERE id = ?");
    $stmt->bind_param("i", $rid);
    if ($stmt->execute()) {
        // Create notification for recipe owner
        $rStmt = $con->prepare("SELECT userId, recipeTitle FROM recipes WHERE id = ?");
        $rStmt->bind_param("i", $rid);
        $rStmt->execute();
        $rRow = $rStmt->get_result()->fetch_assoc();
        $rStmt->close();
        if ($rRow && $rRow['userId']) {
            $msg = sprintf(__('Your recipe "%s" has been approved and is now public!'), $rRow['recipeTitle']);
            $nStmt = $con->prepare("INSERT INTO notifications (user_id, type, message, recipe_id) VALUES (?, 'recipe_approved', ?, ?)");
            $nStmt->bind_param("isi", $rRow['userId'], $msg, $rid);
            $nStmt->execute();
            $nStmt->close();
        }
        $_SESSION['frs_toast_msg'] = __('Recipe approved successfully.');
        $_SESSION['frs_toast_type'] = 'success';
        header('Location: listed-recipes.php');
        exit;
    }
    $stmt->close();
}

// Reject recipe
if (isset($_GET['action']) && $_GET['action'] == 'reject') {
    $rid = isset($_GET['bsid']) ? intval($_GET['bsid']) : 0;
    $stmt = $con->prepare("UPDATE recipes SET status = 0 WHERE id = ?");
    $stmt->bind_param("i", $rid);
    if ($stmt->execute()) {
        $rStmt = $con->prepare("SELECT userId, recipeTitle FROM recipes WHERE id = ?");
        $rStmt->bind_param("i", $rid);
        $rStmt->execute();
        $rRow = $rStmt->get_result()->fetch_assoc();
        $rStmt->close();
        if ($rRow && $rRow['userId']) {
            $msg = sprintf(__('Your recipe "%s" needs revision before publishing.'), $rRow['recipeTitle']);
            $nStmt = $con->prepare("INSERT INTO notifications (user_id, type, message, recipe_id) VALUES (?, 'recipe_rejected', ?, ?)");
            $nStmt->bind_param("isi", $rRow['userId'], $msg, $rid);
            $nStmt->execute();
            $nStmt->close();
        }
        $_SESSION['frs_toast_msg'] = __('Recipe rejected.');
        $_SESSION['frs_toast_type'] = 'warning';
        header('Location: listed-recipes.php');
        exit;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('Listed Recipes'); ?></title>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="table-agile-info">
            <div class="card">
                <div class="card-header">
                    <?php _e('Manage Recipes Details'); ?>
                </div>
                <div>
                    <table class="table" ui-jq="footable" ui-options='{"paging":{"enabled":true},"filtering":{"enabled":true},"sorting":{"enabled":true}}'>
                        <thead>
                            <tr>
                                <th data-breakpoints="xs">S.NO</th>
                                <th><?php _e('Recipe Title'); ?></th>
                                <th><?php _e('Recipe Prep. Time'); ?></th>
                                <th><?php _e('Recipe Cook Time'); ?></th>
                                <th><?php _e('Recipe Yields'); ?></th>
                                <th><?php _e('Listing Date'); ?></th>
                                <th data-breakpoints="xs"><?php _e('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ret = mysqli_query($con, "SELECT r.*, u.FullName FROM recipes r LEFT JOIN users u ON u.ID = r.userId ORDER BY r.status ASC, r.id DESC");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                            $statusLabel = $row['status'] == 1
                                ? '<span class="badge bg-success">' . __('Published') . '</span>'
                                : ($row['status'] == 0
                                    ? '<span class="badge bg-danger">' . __('Rejected') . '</span>'
                                    : '<span class="badge bg-warning text-dark">' . __('Pending') . '</span>');
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td><?php echo htmlspecialchars($row['recipeTitle']); ?></td>
                                <td><?php echo htmlspecialchars($row['FullName'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                                <td><?php echo $statusLabel; ?></td>
                                <td>
                                    <?php if($row['status'] != 1): ?>
                                    <a href="listed-recipes.php?action=approve&bsid=<?php echo intval($row['id']); ?>" class="btn btn-success btn-sm"><?php _e('Approve'); ?></a>
                                    <?php endif; ?>
                                    <?php if($row['status'] != 0): ?>
                                    <a href="listed-recipes.php?action=reject&bsid=<?php echo intval($row['id']); ?>" class="btn btn-warning btn-sm" onclick="return confirm('<?php echo addslashes(__('Reject this recipe?')); ?>');"><?php _e('Reject'); ?></a>
                                    <?php endif; ?>
                                    <a href="edit-recipe.php?recipeid=<?php echo intval($row['id']); ?>" class="btn btn-primary btn-sm"><?php _e('Edit'); ?></a>
                                    <a href="listed-recipes.php?action=delete&bsid=<?php echo intval($row['id']); ?>" title="Delete this record" onclick="return confirm('<?php echo addslashes(__('Do you really want to delete this record?')); ?>');" class="btn btn-danger btn-sm"><?php _e('Delete'); ?></a>
                                </td>
                            </tr>
                        <?php $cnt++; } ?>
                        </tbody>
                    </table>
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
