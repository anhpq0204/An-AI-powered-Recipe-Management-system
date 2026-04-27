<?php  
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
  header('location:logout.php');
  } else{

?>


<!DOCTYPE html>
<head>
<title>Food Recipe System | Regd. User Details </title>
</head>
<body>
<section id="container">
<!--header start-->
<?php include_once('includes/header.php');?>
<!--header end-->
<!--sidebar start-->
<?php include_once('includes/sidebar.php');?>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
		<div class="table-agile-info">
 <div class="card">
<?php $fdate=isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
$tdate=isset($_POST['todate']) ? $_POST['todate'] : '';
?>

    <div class="card-header">
   Registered User Report from  <?php echo htmlspecialchars($fdate);?> to <?php echo htmlspecialchars($tdate);?>
    </div>
    <div>
      <table class="table" ui-jq="footable" ui-options='{
        "paging": {
          "enabled": true
        },
        "filtering": {
          "enabled": true
        },
        "sorting": {
          "enabled": true
        }}'>
        <thead>
          <tr>
            <th data-breakpoints="xs">S.NO</th>
            <th>Full Name</th>
   <th>Mobile Number</th>
   <th>Email</th>
  <th>Registration Date</th>
  <th>Action</th>
          
          </tr>
        </thead>
        <?php
$ret=mysqli_query($con,"select * from  users where date(RegDate) between '$fdate' and '$tdate'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
        <tbody>
          <tr data-expanded="true">
            <td><?php echo $cnt;?></td>
              
                  <td><?php  echo htmlspecialchars($row['FullName']);?></td>
                 <td><?php  echo htmlspecialchars($row['MobileNumber']);?></td>
                 <td><?php  echo htmlspecialchars($row['Email']);?></td>
                  <td><?php  echo htmlspecialchars($row['RegDate']);?></td>
                  <td><a href="user-recipes.php?uid=<?php  echo intval($row['ID']);?>&&uname=<?php  echo htmlspecialchars($row['FullName']);?>" class="btn btn-primary" target="blank">Recipes</a></td>
                 
                </tr>
                <?php 
$cnt=$cnt+1;
}?>
 </tbody>
            </table>
            
            
          
    </div>
  </div>
</div>
</section>
 <!-- footer -->
		 <?php include_once('includes/footer.php');?>  
  <!-- / footer -->
</section>

<!--main content end-->
</section>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
<?php }  ?>