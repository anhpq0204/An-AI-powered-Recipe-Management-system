<?php   require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
  header('location:logout.php');
  } else{
// Code for comment Approval   
if(isset($_GET['action']) && $_GET['action']=='approve'){
$cid=isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$query=mysqli_query($con,"update comments set status='1' where id='$cid'");
if($query){
echo "<script>alert('Comment approved successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'approved-comments.php'; </script>";
} else {
echo "<script>alert('Something went wrong. Please try again.');</script>";
}}
// Code for comment reject   
if(isset($_GET['action']) && $_GET['action']=='reject'){
$cid=isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$query=mysqli_query($con,"update comments set status='0' where id='$cid'");
if($query){
echo "<script>alert('Comment rejected successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'rejected-comments.php'; </script>";
} else {
echo "<script>alert('Something went wrong. Please try again.');</script>";
}}
?>


<!DOCTYPE html>
<head>
<title>Food Recipe System | All Comments </title>

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
    Manage All Comments
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
            <th data-breakpoints="xs">#</th>
            <th>Recipe Title</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Comment Date</th>

            <th data-breakpoints="xs">Action</th>
           
           
          </tr>
        </thead>
        <?php
$ret=mysqli_query($con,"SELECT recipes.recipeTitle,recipes.id as rid, comments.* FROM `comments` join recipes on recipes.id=comments.recipeId");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {
?>
        <tbody>
          <tr data-expanded="true">
            <td><?php echo $cnt;?></td>
            <td><a href="edit-recipe.php?recipeid=<?php echo intval($row['rid']);?>" target="blank"><?php  echo htmlspecialchars($row['recipeTitle']);?></a></td>
            <td><?php  echo htmlspecialchars($row['userName']);?></td>
              <td><?php  echo htmlspecialchars($row['userEmail']);?></td>
              <td><?php  echo htmlspecialchars($row['commentMessage']);?></td>
              <td><?php $status=$row['status'];?>
               <?php if($status==''):?>
                <button class="btn btn-warning btn-sm">Waiting for Approval</button>
               <?php elseif($status=='0'): ?>
                <button class="btn btn-danger btn-sm">Rejected</button>
                <?php else: ?>
                  <button class="btn btn-success btn-sm">Approved</button>
                <?php endif;?>

              </td>
                  <td><?php  echo htmlspecialchars($row['postingDate']);?></td>
                  <td>
                    <?php if($status==''):?>
                     <a href="new-comments.php?action=approve&&cid=<?php echo intval($row['id']); ?>"  title="Approve this comment" onclick="return confirm('Do you really want to approve this comment?');" class="btn btn-success btn-sm">Approve </a>
                    <a href="new-comments.php?action=reject&&cid=<?php echo intval($row['id']); ?>"  title="Reject this comment" onclick="return confirm('Do you really want to reject this comment?');" class="btn btn-danger btn-sm">Reject </a>
                  <?php elseif($status=='0'): ?>
                     <a href="new-comments.php?action=approve&&cid=<?php echo intval($row['id']); ?>"  title="Approve this comment" onclick="return confirm('Do you really want to approve this comment?');" class="btn btn-success btn-sm">Approve </a>
<?php else: ?>
                      <a href="new-comments.php?action=reject&&cid=<?php echo intval($row['id']); ?>"  title="Reject this comment" onclick="return confirm('Do you really want to reject this comment?');" class="btn btn-danger btn-sm">Reject </a>
                    <?php endif;?>

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
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
<?php }  ?>