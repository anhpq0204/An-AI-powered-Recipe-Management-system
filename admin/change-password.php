<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
require_once('../includes/auth.php');

if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

$adminid = intval($_SESSION['frsaid']);
$msg = "";
$msgType = "";

if (isset($_POST['submit'])) {
    $stmt = $con->prepare("SELECT Password FROM admins WHERE ID = ?");
    $stmt->bind_param("i", $adminid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && frs_password_verify($_POST['currentpassword'], $row['Password'])) {
        $newpassword = frs_password_hash($_POST['newpassword']);
        $up = $con->prepare("UPDATE admins SET Password = ? WHERE ID = ?");
        $up->bind_param("si", $newpassword, $adminid);
        $up->execute();
        $up->close();
        $msg = __('Password successfully changed.');
        $msgType = "success";
    } else {
        $msg = __('Your current password is wrong.');
        $msgType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | Change Password</title>
    <script type="text/javascript">
        function checkpass() {
            if (document.changepassword.newpassword.value != document.changepassword.confirmpassword.value) {
                alert('<?php echo addslashes(__('New Password and Confirm Password field does not match')); ?>');
                document.changepassword.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">
        <h1 class="page-title" style="margin-bottom: 20px;">
            <?php _e('Security Settings'); ?>
            <small style="display: block; font-size: 14px; font-weight: normal; color: var(--text-muted);"><?php _e('Ensure your admin account is secure'); ?></small>
        </h1>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <header class="card-header">
                        <?php _e('Change Password'); ?>
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form class="cmxform form-horizontal" method="post" action="" name="changepassword" onsubmit="return checkpass();">
                            <div class="form-group row mb-3">
                                <label for="currentpassword" class="control-label col-lg-4"><?php _e('Current Password'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="currentpassword" name="currentpassword" type="password" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="newpassword" class="control-label col-lg-4"><?php _e('New Password'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="newpassword" name="newpassword" type="password" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="confirmpassword" class="control-label col-lg-4"><?php _e('Confirm Password'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="confirmpassword" name="confirmpassword" type="password" required>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary px-4" type="submit" name="submit"><?php _e('Change Password'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="js/jquery.nicescroll.js"></script>
</body>
</html>
