<?php
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
        $msg="Invalid Admin Details. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Admin Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <!-- Modern UI CSS -->
    <link href="../css/modern.css" rel="stylesheet">
    
    <style>
        /* Admin specific override to differentiate from regular users */
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
                <h2>Admin Portal</h2>
                <p>Sign in to manage the Food Recipe System</p>
            </div>
            
            <?php if($msg) { ?>
            <div class="auth-alert">
                <?php echo $msg; ?>
            </div>
            <?php } ?>

            <form action="" method="post" name="login" class="auth-form">
                <div class="form-group-modern">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group-modern">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="forgot-password.php" style="color: var(--gray-700); font-size: 14px; text-decoration: none;">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-modern btn-primary-modern btn-admin">Sign In as Admin</button>
            </form>

            <div class="auth-footer">
                <a href="../index.php" class="auth-home-link"><i class="fa fa-home"></i> Back to Frontend</a>
            </div>
        </div>
    </div>
</body>
</html>
