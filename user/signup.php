<?php 
require_once('../includes/session.php');
include('../includes/dbconnection.php');

$msg = "";
$msgClass = "auth-alert"; // Default error class
if(isset($_POST['submit'])) {
    $fname=$_POST['name'];
    $mobno=$_POST['mobilnumber'];
    $email=$_POST['email'];
    $password=md5($_POST['password']);

    $ret=mysqli_query($con, "select Email from users where Email='$email'");
    $result=mysqli_fetch_array($ret);
    if($result>0){
        $msg="This email or Contact Number is already associated with another account.";
    } else {
        $query=mysqli_query($con, "insert into users(FullName, MobileNumber, Email, Password) value('$fname', '$mobno', '$email', '$password' )");
        if ($query) {
            $msg="You have successfully registered. You can now login.";
            $msgClass = "auth-alert auth-success"; // Success class
        } else {
            $msg="Something Went Wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | User Registration</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <!-- Modern UI CSS -->
    <link href="../css/modern.css" rel="stylesheet">
    <script type="text/javascript">
    function checkpass() {
        if(document.signup.password.value != document.signup.repeatpassword.value) {
            alert('Password and Repeat Password fields do not match');
            document.signup.repeatpassword.focus();
            return false;
        }
        return true;
    } 
    </script>
</head>
<body>
    <div class="auth-wrapper" style="background-image: url('../img/bg-img/bg6.jpg');">
        <div class="auth-overlay"></div>
        <div class="auth-card auth-card-signup">
            <div class="auth-header">
                <h2>Join Our Community</h2>
                <p>Register to share and discover amazing recipes</p>
            </div>
            
            <?php if($msg) { ?>
            <div class="<?php echo $msgClass; ?>">
                <?php echo $msg; ?>
            </div>
            <?php } ?>

            <form action="" method="post" name="signup" class="auth-form" onsubmit="return checkpass();">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group-modern">
                            <input type="text" name="name" placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group-modern">
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group-modern">
                            <input type="text" name="mobilnumber" placeholder="Phone Number" required maxlength="10" pattern="[0-9]+">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group-modern">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group-modern">
                            <input type="password" name="repeatpassword" placeholder="Repeat Password" required>
                        </div>
                    </div>
                </div>

                <div style="margin: 10px 0 20px;">
                    <label style="color: var(--gray-700); font-size: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" required style="width: 18px; height: 18px; accent-color: var(--primary);"> 
                        I agree to the Terms of Service and Privacy Policy
                    </label>
                </div>

                <button type="submit" name="submit" class="btn-modern btn-primary-modern">Register Now</button>
            </form>

            <div class="auth-footer">
                <p>Already Registered? <a href="login.php">Login Here</a></p>
                <a href="../index.php" class="auth-home-link"><i class="fa fa-home"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
