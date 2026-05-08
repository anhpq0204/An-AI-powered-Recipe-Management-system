<?php   require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
  header('location:logout.php');
  } else{
 // Code for deletion   
if(isset($_GET['action']) && $_GET['action']=='delete'){
$rid=isset($_GET['bsid']) ? intval($_GET['bsid']) : 0;
$query=mysqli_query($con,"delete from recipes where id='$rid'");
if($query){
unlink($ppicpath);
echo "<script>alert('Recipe deleted successfully.');</script>";
echo "<script type='text/javascript'> document.location = 'manage-recipes.php'; </script>";
} else {
echo "<script>alert('Something went wrong. Please try again.');</script>";
}

}
?>


<!DOCTYPE html>
<head>
<title>Food Recipe System | Manage Recipe Detail </title>

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
   Recipes Report from  <?php echo htmlspecialchars($fdate);?> to <?php echo htmlspecialchars($tdate);?>
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
            <th>Recipe Title</th>
            <th>Recipe Prep. Time</th>
            <th>Recipe Cook Time</th>
            <th>Recipe Yields</th>
            <th>Listing Date</th>
            <th data-breakpoints="xs">Action</th>
           
           
          </tr>
        </thead>
        <?php
$ret=mysqli_query($con,"select * from  recipes where date(postingDate) between '$fdate' and '$tdate'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {
?>
        <tbody>
          <tr data-expanded="true">
            <td><?php echo $cnt;?></td>
            <td><?php  echo htmlspecialchars($row['recipeTitle']);?></td>
            <td><?php  echo htmlspecialchars($row['recipePrepTime']);?> Minutes</td>
              <td><?php  echo htmlspecialchars($row['recipeCookTime']);?> Minutes</td>
              <td><?php  echo htmlspecialchars($row['recipeYields']);?> Serves</td>
                  <td><?php  echo htmlspecialchars($row['postingDate']);?></td>
                  <td><a href="edit-recipe.php?recipeid=<?php echo intval($row['id']);?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="manage-food-details.php?action=delete&&bsid=<?php echo intval($row['ID']); ?>"  title="Delete this record" onclick="return confirm('Do you really want to delete this record?');" class="btn btn-danger btn-sm">Delete </a>
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