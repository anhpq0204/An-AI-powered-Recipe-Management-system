<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if(isset($_POST['submit']))
  {
    $contactno=$_POST['contactno'];
    $email=$_POST['email'];

        $query=mysqli_query($con,"select ID from users where  Email='$email' and MobileNumber='$contactno' ");
    $ret=mysqli_fetch_array($query);
    if($ret>0){
      $_SESSION['contactno']=$contactno;
      $_SESSION['email']=$email;
     header('location:reset-password.php');
    }
    else{
      echo "<script>alert('Invalid Details. Please try again.');</script>";
    }
  }
  ?>
<!DOCTYPE html>
<head>
<title>Food Recipe System| Forgot Password </title>

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
    <p>Already Registered.<a href="login.php">Login</a></p>
     <p class="mb-1">
   
     <i class="fa fa-home" aria-hidden="true"><a href="../index.php">Home Page</a></i>
      </p>
</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
