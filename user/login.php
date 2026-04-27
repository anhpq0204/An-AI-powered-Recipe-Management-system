<?php
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
      header('location:dashboard.php');
      exit;
    } else {
      $msg="Invalid Details. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | User Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <!-- Modern UI CSS -->
    <link href="../css/modern.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('../img/bg-img/bg1.jpg');">
        <div class="auth-overlay"></div>
        <div class="auth-card">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your recipe collection</p>
            </div>
            
            <?php if($msg) { ?>
            <div class="auth-alert">
                <?php echo $msg; ?>
            </div>
            <?php } ?>

            <form action="" method="post" name="login" class="auth-form">
                <div class="form-group-modern">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="form-group-modern">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="forgot-password.php" style="color: var(--primary); font-size: 14px; text-decoration: none;">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-modern btn-primary-modern">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Create an account</a></p>
                <a href="../index.php" class="auth-home-link"><i class="fa fa-home"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
