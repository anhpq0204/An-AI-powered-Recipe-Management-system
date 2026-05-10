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

if (isset($_POST['submit'])) {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $mobno = mysqli_real_escape_string($con, $_POST['contactnumber']);

    $query = mysqli_query($con, "UPDATE users SET FullName='$fullname', MobileNumber='$mobno' WHERE ID='$uid'");
    if ($query) {
        $msg = __('Profile details updated successfully.');
    } else {
        $msg = __('Something went wrong. Please try again.');
    }
}

$ret = mysqli_query($con, "SELECT * FROM users WHERE ID='$uid'");
$userProfile = mysqli_fetch_array($ret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | User Profile</title>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">
        <h1 class="user-page-title">
            <?php _e('Profile Settings'); ?>
            <small><?php _e('Manage your personal information'); ?></small>
        </h1>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <header class="card-header">
                        <?php _e('User Profile'); ?>
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert <?php echo strpos($msg, 'successfully') !== false || strpos($msg, 'thành công') !== false ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($userProfile): ?>
                        <form class="cmxform form-horizontal" method="post" action="">
                            <div class="form-group row mb-3">
                                <label for="fullname" class="control-label col-lg-4"><?php _e('Full Name'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="fullname" name="fullname" type="text" value="<?php echo htmlspecialchars($userProfile['FullName']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="email" class="control-label col-lg-4"><?php _e('Email'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="email" name="email" type="email" value="<?php echo htmlspecialchars($userProfile['Email']); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="contactnumber" class="control-label col-lg-4"><?php _e('Mobile Number'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="contactnumber" name="contactnumber" type="text" value="<?php echo htmlspecialchars($userProfile['MobileNumber']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="regDate" class="control-label col-lg-4"><?php _e('Registration Date'); ?></label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="regDate" name="regdate" type="text" value="<?php echo htmlspecialchars($userProfile['RegDate']); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary" type="submit" name="submit"><?php _e('Update Profile'); ?></button>
                                </div>
                            </div>
                        </form>
                        <?php else: ?>
                            <p class="text-danger"><?php _e('Unable to load profile data.'); ?></p>
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
