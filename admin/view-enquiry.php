<?php  require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
  header('location:logout.php');
  } else{
if(isset($_POST['submit'])){
  $aremark=$_POST['adminremark'];
   $eid=intval($_GET['enqid']);
  $query=mysqli_query($con, "update   enquiries set adminRemark ='$aremark' where id='$eid'");
echo "<script>alert('Comment rejected successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'readenq.php'; </script>";

}


?>


<!DOCTYPE html>
<head>
<title>Food Recipe System | View Enquiry </title>
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
    <div class="card-header">
     View Enquiry Details
    </div>
    <div><?php
             $eid=intval($_GET['enqid']);
$ret=mysqli_query($con,"select * from enquiries where id=$eid");
while ($row=mysqli_fetch_array($ret)) {

?>
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
        
              
                <tr>
    <th scope style="font-size: 15px;">Name</th>
    <td><?php  echo htmlspecialchars($row['userName']);?></td>
    <th style="font-size: 15px;" scope>Email</th>
    <td><?php  echo htmlspecialchars($row['userEmail']);?></td>
  </tr>
  <tr>
   <th style="font-size: 15px;" scope>Subject</th>
    <td><?php  echo htmlspecialchars($row['subject']);?></td>
       <th style="font-size: 15px;" scope>Enq. Posting Date</th>
    <td><?php  echo htmlspecialchars($row['postingDate']);?></td>
                </tr>
                <tr>
    
    <th style="font-size: 15px;">Message</th>
    <td colspan="4"><?php  echo htmlspecialchars($row['commentMessage']);?></td>
  </tr>
<?php if($row['adminRemark']!=''): ?>
              <tr>
    
    <th style="font-size: 15px;">Admin Remark</th>
    <td colspan="4"><?php  echo htmlspecialchars($row['adminRemark']);?></td>
  </tr>
  <tr>
   <th style="font-size: 15px;" scope>Admin Remark Date</th>
    <td><?php  echo htmlspecialchars($row['updationDate']);?></td>
  <?php endif; if($row['adminRemark']==''): ?>
  <form method="post">
  <tr>
       <th style="font-size: 15px;">Admin Remark</th>
    <td colspan="4">
      <textarea class="form-control" name="adminremark" rows="5" required></textarea>
    </td>

  </tr>
  <tr>
    <td><input class="btn btn-primary" type="submit" name="submit"></td>
  </tr>
</form>
<?php endif;?>
            </table><?php $cnt=$cnt+1;} ?>
            
            
          
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
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
<?php }  ?>