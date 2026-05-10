<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

// Code for comment Approval
if (isset($_GET['action']) && $_GET['action'] == 'approve') {
    $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    $query = mysqli_query($con, "UPDATE comments SET status='1' WHERE id='$cid'");
    if ($query) {
        echo "<script>alert('" . addslashes(__('Comment approved successfully.')) . "');</script>";
        echo "<script type='text/javascript'> document.location = 'approved-comments.php'; </script>";
    } else {
        echo "<script>alert('" . addslashes(__('Something went wrong. Please try again.')) . "');</script>";
    }
}
// Code for comment reject
if (isset($_GET['action']) && $_GET['action'] == 'reject') {
    $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    $query = mysqli_query($con, "UPDATE comments SET status='0' WHERE id='$cid'");
    if ($query) {
        echo "<script>alert('" . addslashes(__('Comment rejected successfully.')) . "');</script>";
        echo "<script type='text/javascript'> document.location = 'rejected-comments.php'; </script>";
    } else {
        echo "<script>alert('" . addslashes(__('Something went wrong. Please try again.')) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('All Comments'); ?></title>
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
                    <?php _e('Manage All Comments'); ?>
                </div>
                <div>
                    <table class="table" ui-jq="footable" ui-options='{"paging":{"enabled":true},"filtering":{"enabled":true},"sorting":{"enabled":true}}'>
                        <thead>
                            <tr>
                                <th data-breakpoints="xs">#</th>
                                <th><?php _e('Recipe Title'); ?></th>
                                <th><?php _e('User Name'); ?></th>
                                <th><?php _e('Email'); ?></th>
                                <th><?php _e('Comment'); ?></th>
                                <th><?php _e('Status'); ?></th>
                                <th><?php _e('Comment Date'); ?></th>
                                <th data-breakpoints="xs"><?php _e('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ret = mysqli_query($con, "SELECT recipes.recipeTitle, recipes.id as rid, comments.* FROM `comments` JOIN recipes ON recipes.id = comments.recipeId");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td><a href="edit-recipe.php?recipeid=<?php echo intval($row['rid']); ?>" target="blank"><?php echo htmlspecialchars($row['recipeTitle']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['userName']); ?></td>
                                <td><?php echo htmlspecialchars($row['userEmail']); ?></td>
                                <td><?php echo htmlspecialchars($row['commentMessage']); ?></td>
                                <td><?php $status = $row['status']; ?>
                                    <?php if ($status == ''): ?>
                                        <button class="btn btn-warning btn-sm"><?php _e('Waiting for Approval'); ?></button>
                                    <?php elseif ($status == '0'): ?>
                                        <button class="btn btn-danger btn-sm"><?php _e('Rejected'); ?></button>
                                    <?php else: ?>
                                        <button class="btn btn-success btn-sm"><?php _e('Approved'); ?></button>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                                <td>
                                    <?php if ($status == ''): ?>
                                        <a href="new-comments.php?action=approve&cid=<?php echo intval($row['id']); ?>" title="Approve this comment" onclick="return confirm('<?php echo addslashes(__('Do you really want to approve this comment?')); ?>');" class="btn btn-success btn-sm"><?php _e('Approve'); ?></a>
                                        <a href="new-comments.php?action=reject&cid=<?php echo intval($row['id']); ?>" title="Reject this comment" onclick="return confirm('<?php echo addslashes(__('Do you really want to reject this comment?')); ?>');" class="btn btn-danger btn-sm"><?php _e('Reject'); ?></a>
                                    <?php elseif ($status == '0'): ?>
                                        <a href="new-comments.php?action=approve&cid=<?php echo intval($row['id']); ?>" title="Approve this comment" onclick="return confirm('<?php echo addslashes(__('Do you really want to approve this comment?')); ?>');" class="btn btn-success btn-sm"><?php _e('Approve'); ?></a>
                                    <?php else: ?>
                                        <a href="new-comments.php?action=reject&cid=<?php echo intval($row['id']); ?>" title="Reject this comment" onclick="return confirm('<?php echo addslashes(__('Do you really want to reject this comment?')); ?>');" class="btn btn-danger btn-sm"><?php _e('Reject'); ?></a>
                                    <?php endif; ?>
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
