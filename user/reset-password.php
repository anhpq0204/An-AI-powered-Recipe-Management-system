<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if(isset($_POST['submit']))
  {
    $contactno=$_SESSION['contactno'];
    $email=$_SESSION['email'];
    $password=md5($_POST['newpassword']);

        $query=mysqli_query($con,"update users set Password='$password'  where  Email='$email' && MobileNumber='$contactno' ");
   if($query)
   {
echo "<script>alert('Password successfully changed');</script>";
session_destroy();
   }
  
  }
  ?>


<!DOCTYPE html>
<head>
<title>Food Recipe System | Forgot </title>

<script src="assets/js/modernizr.min.js"></script>
<script type="text/javascript">
function checkpass()
{
if(document.changepassword.newpassword.value!=document.changepassword.confirmpassword.value)
{
alert('New Password and Confirm Password field does not match');
document.changepassword.confirmpassword.focus();
return false;
}
return true;
} 

</script>
</head>
<body>
<div class="log-w3">
<div class="w3layouts-main">
	<h2>Food Recipe  System</h2>
	  <hr />
	<h3 align="center">Password Recovery</h3>
		<form action="" method="post" name="changepassword" onsubmit="return checkpass();">
			<p style="font-size:16px; color:red" align="center"> <?php if($msg){
    echo $msg;
  }  ?> </p>
			<input class="ggg" type="password" required="true" name="newpassword" placeholder="New Password">
      <input class="ggg" type="password" name="confirmpassword" required="true" placeholder="Confirm Your Password">
			
			
			
				<div class="clearfix"></div>
				<input type="submit" value="Reset" name="submit">
		</form>
		<p><a href="login.php">Sign In</a></p>
		 <p class="mb-1">
   
     <i class="fa fa-home" aria-hidden="true"><a href="../index.php">Home Page</a></i>
      </p>
</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
