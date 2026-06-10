<?php require_once('../includes/session.php');
include('../includes/dbconnection.php');

if(isset($_POST['submit']))
  {
    $contactno=trim($_POST['contactno']);
    $email=trim($_POST['email']);

    $query=$con->prepare("SELECT ID FROM admins WHERE Email = ? AND MobileNumber = ?");
    $query->bind_param("ss", $email, $contactno);
    $query->execute();
    $ret=$query->get_result()->fetch_assoc();
    $query->close();
    if($ret){
      $_SESSION['contactno']=$contactno;
      $_SESSION['email']=$email;
     header('location:reset-password.php');
     exit;
    }
    else{

      echo "<script>window._frsToast=" . json_encode(['msg' => 'Invalid Details. Please try again.', 'type' => 'danger']) . ";</script>";
    }
  }
  ?>
<!DOCTYPE html>
<head>
<title>Food Recipe System | Forgot Password</title>
</head>
<body>
<div class="log-w3">
<div class="w3layouts-main">

  <h2>Food Recipe  System </h2>
  <hr />
  <h3 align="center">Forgot Password</h3>
		<form action="#" method="post" name="submit">
		
			<input type="email" class="ggg" name="email" placeholder="Email" required="true">
       <input class="ggg"  type="text" name="contactno" required="" placeholder="Mobile Number">
			
			
			
				<div class="clearfix"></div>
				<input type="submit" value="Reset" name="submit">
		</form>
		<p><a href="login.php">Sign In</a></p>
    <p class="mb-1">
   
     <i class="fa fa-home" aria-hidden="true"><a href="../index.php">Home Page</a></i>
      </p>
</div>
</div>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
