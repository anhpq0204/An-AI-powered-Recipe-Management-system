<?php   require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
  header('location:logout.php');
  } else{

?>


<!DOCTYPE html>
<head>
<title>Food Recipe System  | Search  Listed Recipes </title>
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
    
    <div>
       <form class="cmxform form-horizontal" method="post" action="" name="search">
                                   
                                    <div class="form-group ">
                                        
                                    </div>
                                    <div class="form-group ">
                                        <label for="username" class="control-label col-lg-3">Search by Recipe Name:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="searchdata" id="searchdata" class="form-control" value="" required="true">
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="col-lg-offset-3 col-lg-6">
                                    <p style="text-align: center;"> <button class="btn btn-primary" type="submit" name="search">Search</button></p>
                                           <p>&nbsp;</p>
                                        </div>
                                    </div>

                                </form>
                                <?php
if(isset($_POST['search']))
{ 

$sdata=$_POST['searchdata'];
  ?>
  
<div class="card-header">
   
          Result against "<?php echo htmlspecialchars($sdata);?>" keyword</div>

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
          
          <?php $uid=$_SESSION['frsuid'];
$ret=mysqli_query($con,"select * from  recipes where recipeTitle like '%$sdata%' && userId='$uid'");
$count=mysqli_num_rows($ret);
$cnt=1;
if($count>0){
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
}} else {?>
<tr>
  <td colspan="9" style="color:red">No Record Found</td>
</tr>

<?php } ?>  
 </tbody>
            </table>
            <?php } ?>
            
          
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