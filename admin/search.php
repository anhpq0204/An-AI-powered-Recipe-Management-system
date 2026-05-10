<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('Search Listed Recipes'); ?></title>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="table-agile-info">
            <div class="card">
                <div>
                    <form class="cmxform form-horizontal" method="post" action="" name="search">
                        <div class="form-group">
                            <label for="searchdata" class="control-label col-lg-3"><?php _e('Search by Recipe Name:'); ?></label>
                            <div class="col-lg-6">
                                <input type="text" name="searchdata" id="searchdata" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-6">
                                <p style="text-align:center;"><button class="btn btn-primary" type="submit" name="search"><?php _e('Search'); ?></button></p>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($_POST['search'])): ?>
                    <?php $sdata = $_POST['searchdata']; ?>
                    <div class="card-header">
                        <?php _e('Result against'); ?> "<?php echo htmlspecialchars($sdata); ?>" <?php _e('keyword'); ?>
                    </div>
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
                        $ret = mysqli_query($con, "SELECT * FROM recipes WHERE recipeTitle LIKE '%$sdata%'");
                        $count = mysqli_num_rows($ret);
                        $cnt = 1;
                        if ($count > 0) {
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
                        <?php $cnt++; }
                        } else { ?>
                            <tr>
                                <td colspan="9" style="color:red;"><?php _e('No Record Found'); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
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
</body>
</html>
