<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
require_once('../includes/auth.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = intval($_SESSION['frsuid']);
$msg = "";

if (isset($_POST['submit'])) {
    $stmt = $con->prepare("SELECT Password FROM users WHERE ID = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && frs_password_verify($_POST['currentpassword'], $row['Password'])) {
        $newpassword = frs_password_hash($_POST['newpassword']);
        $up = $con->prepare("UPDATE users SET Password = ? WHERE ID = ?");
        $up->bind_param("si", $newpassword, $uid);
        $up->execute();
        $up->close();
        $msg = __('Your password successfully changed.');
    } else {
        $msg = __('Your current password is wrong.');
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
        <h1 class="user-page-title">
            <?php _e('Security Settings'); ?>
            <small><?php _e('Update your account password'); ?></small>
        </h1>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <header class="card-header">
                        <?php _e('Change Password'); ?>
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert <?php echo strpos($msg, 'successfully') !== false || strpos($msg, 'thành công') !== false ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>

                        <form class="cmxform form-horizontal" method="post" action="" name="changepassword" onsubmit="return checkpass();">
                            <div class="form-group row mb-3">
                                <label for="currentpassword" class="control-label col-lg-4"><?php _e('Current Password'); ?></label>
                                <div class="col-lg-8">
                                    <input type="password" name="currentpassword" id="currentpassword" class="form-control" required="true">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="newpassword" class="control-label col-lg-4"><?php _e('New Password'); ?></label>
                                <div class="col-lg-8">
                                    <input type="password" name="newpassword" id="newpassword" class="form-control" required="true">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="confirmpassword" class="control-label col-lg-4"><?php _e('Confirm Password'); ?></label>
                                <div class="col-lg-8">
                                    <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" required="true">
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary" type="submit" name="submit"><?php _e('Change Password'); ?></button>
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
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
