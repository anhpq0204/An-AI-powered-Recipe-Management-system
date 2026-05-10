<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

$msg = "";
if(isset($_POST['login'])) {
    $adminuser=$_POST['username'];
    $password=md5($_POST['password']);
    $query=mysqli_query($con,"select ID from admins where UserName='$adminuser' && Password='$password'");
    $ret=mysqli_fetch_array($query);
    if($ret){
      $_SESSION['frsaid']=$ret['ID'];
      header('location:dashboard.php');
      exit;
    } else {
        $msg=__('Invalid Admin Details. Please try again.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Admin Login</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .auth-overlay-admin {
            background: linear-gradient(135deg, rgba(33, 37, 41, 0.95) 0%, rgba(52, 58, 64, 0.9) 100%);
        }
        .btn-admin {
            background: var(--dark);
            border-color: var(--dark);
        }
        .btn-admin:hover {
            background: var(--gray-700);
            border-color: var(--gray-700);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('../img/bg-img/bg7.jpg');">
        <div class="auth-overlay auth-overlay-admin"></div>
        <div class="auth-card">
            <div class="auth-header">
                <h2><?php _e('Admin Portal'); ?></h2>
                <p><?php _e('Sign in to manage the Food Recipe System'); ?></p>
            </div>

            <?php if($msg) { ?>
            <div class="auth-alert">
                <?php echo htmlspecialchars($msg); ?>
            </div>
            <?php } ?>

            <form action="" method="post" name="login" class="auth-form">
                <div class="form-group-modern">
                    <input type="text" name="username" placeholder="<?php echo htmlspecialchars(__('Username')); ?>" required>
                </div>
                <div class="form-group-modern">
                    <input type="password" name="password" placeholder="<?php echo htmlspecialchars(__('Password')); ?>" required>
                </div>

                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="forgot-password.php" style="color: var(--gray-700); font-size: 14px; text-decoration: none;"><?php _e('Forgot Password?'); ?></a>
                </div>

                <button type="submit" name="login" class="btn-modern btn-primary-modern btn-admin"><?php _e('Sign In as Admin'); ?></button>
            </form>

            <div class="auth-footer">
                <a href="../index.php" class="auth-home-link"><i class="fa fa-home"></i> <?php _e('Back to Frontend'); ?></a>
            </div>
        </div>
    </div>
</body>
</html>
