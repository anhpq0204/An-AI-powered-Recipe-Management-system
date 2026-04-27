<?php require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
 header('location:logout.php');
  } else{
   
  ?>

<!DOCTYPE html>
<head>
<title>Food Recipe System | Reg User Reports  </title>

<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
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
		<div class="form-w3layouts">
            <!-- page start-->
            
          
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="card-header">
                           Recipes B/w Dates Report Date Selection
                            <span class="tools float-end">
                                <a class="fa fa-chevron-down" href="javascript:;"></a>
                                <a class="fa fa-cog" href="javascript:;"></a>
                                <a class="fa fa-times" href="javascript:;"></a>
                             </span>
                        </header>
                        <div class="card-body">
                            <div class="form">
   
                                <form class="cmxform form-horizontal " method="post" name="bwdatesreportds" action="recipes-report-details.php">
                                   
                                    <div class="form-group ">
                                        <label for="adminname" class="control-label col-lg-3">From Dates</label>
                                        <div class="col-lg-6">
                                            <input class="form-control" id="fromdate" name="fromdate"  type="date" required="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="adminname" class="control-label col-lg-3">To Dates
                                        </label>
                                        <div class="col-lg-6">
                                           <input class="form-control " id="todate" type="date" name="todate" required="true">
                                        </div>
                                    </div>
                                  
                                    <div class="form-group">
                                        <div class="col-lg-offset-3 col-lg-6">
                                          <p style="text-align: center;"> <button class="btn btn-primary" type="submit" name="submit">Update</button></p>
                                           
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </section>
                </div>
            </div>
            <!-- page end-->
        </div>

</section>
 <!-- footer -->
		  <?php include_once('includes/footer.php');?>    
  <!-- / footer -->
</section>

</section>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
<?php }  ?>