<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

$msg = "";
if(isset($_POST['login'])) {
    $email=$_POST['email'];
    $password=md5($_POST['password']);
    $query=mysqli_query($con,"select ID from users where  Email='$email' && Password='$password' ");
    $ret=mysqli_fetch_array($query);
    if($ret){
      $_SESSION['frsuid']=$ret['ID'];
      header('location:../index.php');
      exit;
    } else {
      $msg=__('Invalid Details. Please try again.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | User Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('../img/bg-img/bg1.jpg');">
        <div class="auth-overlay"></div>
        <div class="auth-card">
            <div class="auth-header">
                <h2><?php _e('Welcome Back'); ?></h2>
                <p><?php _e('Sign in to access your recipe collection'); ?></p>
            </div>

            <?php if($msg) { ?>
            <div class="auth-alert">
                <?php echo htmlspecialchars($msg); ?>
            </div>
            <?php } ?>

            <form action="" method="post" name="login" class="auth-form">
                <div class="form-group-modern">
                    <input type="email" name="email" placeholder="<?php echo htmlspecialchars(__('Email Address')); ?>" required>
                </div>
                <div class="form-group-modern">
                    <input type="password" name="password" placeholder="<?php echo htmlspecialchars(__('Password')); ?>" required>
                </div>

                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="forgot-password.php" style="color: var(--primary); font-size: 14px; text-decoration: none;"><?php _e('Forgot Password?'); ?></a>
                </div>

                <button type="submit" name="login" class="btn-modern btn-primary-modern"><?php _e('Sign In'); ?></button>
            </form>

            <div class="auth-footer">
                <p><?php _e("Don't have an account? Create an account"); ?></p>
                <a href="../index.php" class="auth-home-link"><i class="fa fa-home"></i> <?php _e('Back to Home'); ?></a>
            </div>
        </div>
    </div>
</body>
</html>
