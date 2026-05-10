<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['frsuid'];
$msg = "";

// ── XỬ LÝ GET XÓA RECIPE ─────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $rid = isset($_GET['bsid']) ? intval($_GET['bsid']) : 0;

    // Ensure user can only delete their own recipe
    $query = mysqli_query($con, "DELETE FROM recipes WHERE id='$rid' AND userId='$uid'");
    if ($query) {
        $msg = __('Recipe deleted successfully.');
    } else {
        $msg = __('Something went wrong. Please try again.');
    }
}

// ── LẤY DANH SÁCH RECIPE ─────────────────────────────────
$ret = mysqli_query($con, "SELECT * FROM recipes WHERE userId='$uid' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | <?php _e('Manage Recipes'); ?></title>
</head>
<body>
<section id="container">

<!-- Header -->
<?php include_once('includes/header.php');?>
<!-- Sidebar -->
<?php include_once('includes/sidebar.php');?>

<!-- Main Content -->
<section id="main-content">
    <section class="wrapper">
        <h1 class="user-page-title">
            <?php _e('Manage Recipes'); ?>
            <small><?php _e('View, edit or delete your listed recipes'); ?></small>
        </h1>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <header class="card-header">
                        <?php _e('My Recipes'); ?>
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert <?php echo (strpos($msg, 'successfully') !== false || strpos($msg, 'thành công') !== false) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th><?php _e('Recipe Title'); ?></th>
                                        <th><?php _e('Prep Time'); ?></th>
                                        <th><?php _e('Cook Time'); ?></th>
                                        <th><?php _e('Yields'); ?></th>
                                        <th><?php _e('Listing Date'); ?></th>
                                        <th><?php _e('Action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $cnt = 1;
                                if (mysqli_num_rows($ret) > 0) {
                                    while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td><?php echo htmlspecialchars($row['recipeTitle']);?></td>
                                        <td><?php echo htmlspecialchars($row['recipePrepTime']);?> <?php _e('Mins'); ?></td>
                                        <td><?php echo htmlspecialchars($row['recipeCookTime']);?> <?php _e('Mins'); ?></td>
                                        <td><?php echo htmlspecialchars($row['recipeYields']);?> <?php _e('Serves'); ?></td>
                                        <td><?php echo htmlspecialchars($row['postingDate']);?></td>
                                        <td>
                                            <a href="edit-recipe.php?recipeid=<?php echo intval($row['id']);?>" class="btn btn-primary btn-sm"><?php _e('Edit'); ?></a>
                                            <a href="manage-recipes.php?action=delete&bsid=<?php echo intval($row['id']); ?>" title="Delete this record" onclick="return confirm('<?php echo addslashes(__('Do you really want to delete this recipe?')); ?>');" class="btn btn-danger btn-sm"><?php _e('Delete'); ?></a>
                                        </td>
                                    </tr>
                                <?php
                                        $cnt = $cnt + 1;
                                    }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center"><?php _e('No recipes found.'); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
