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
        echo "<script>alert('" . addslashes(__('Recipe deleted successfully.')) . "');</script>";
        echo "<script type='text/javascript'> document.location = 'listed-recipes.php'; </script>";
    } else {
        echo "<script>alert('" . addslashes(__('Something went wrong. Please try again.')) . "');</script>";
    }
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
                        $ret = mysqli_query($con, "SELECT * FROM recipes");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td><?php echo htmlspecialchars($row['recipeTitle']); ?></td>
                                <td><?php echo htmlspecialchars($row['recipePrepTime']); ?> <?php _e('Minutes'); ?></td>
                                <td><?php echo htmlspecialchars($row['recipeCookTime']); ?> <?php _e('Minutes'); ?></td>
                                <td><?php echo htmlspecialchars($row['recipeYields']); ?> <?php _e('Serves'); ?></td>
                                <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                                <td>
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
