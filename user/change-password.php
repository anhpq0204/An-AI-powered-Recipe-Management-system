<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['frsuid'];
$msg = "";

// ── XỬ LÝ FORM POST ──────────────────────────────────────
if (isset($_POST['submit'])) {
    $cpassword = md5($_POST['currentpassword']);
    $newpassword = md5($_POST['newpassword']);

    $query = mysqli_query($con, "SELECT ID FROM users WHERE ID='$uid' AND Password='$cpassword'");
    $row = mysqli_fetch_array($query);
    if ($row > 0) {
        $ret = mysqli_query($con, "UPDATE users SET Password='$newpassword' WHERE ID='$uid'");
        $msg = "Your password successfully changed.";
    } else {
        $msg = "Your current password is wrong.";
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
                alert('New Password and Confirm Password field does not match');
                document.changepassword.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
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
            Security Settings
            <small>Update your account password</small>
        </h1>
        
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <header class="card-header">
                        Change Password
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert <?php echo strpos($msg, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>

                        <form class="cmxform form-horizontal" method="post" action="" name="changepassword" onsubmit="return checkpass();">
                            <div class="form-group row mb-3">
                                <label for="currentpassword" class="control-label col-lg-4">Current Password</label>
                                <div class="col-lg-8">
                                    <input type="password" name="currentpassword" id="currentpassword" class="form-control" required="true">
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="newpassword" class="control-label col-lg-4">New Password</label>
                                <div class="col-lg-8">
                                    <input type="password" name="newpassword" id="newpassword" class="form-control" required="true">
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="confirmpassword" class="control-label col-lg-4">Confirm Password</label>
                                <div class="col-lg-8">
                                    <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" required="true">
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary" type="submit" name="submit">Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>    
</section>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>