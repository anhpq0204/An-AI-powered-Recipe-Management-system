<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
} else {
    $msg = "";
    if(isset($_POST['submit'])) {
        $pagetitle=$_POST['pagetitle'];
        $pagedes=$_POST['pagedes'];
        $query=mysqli_query($con,"update pages set PageTitle='$pagetitle',PageDescription='$pagedes' where PageType='aboutus'");
        $msg = $query ? __('About Us has been updated.') : __('Something Went Wrong. Please try again.');
    }
?>
<!DOCTYPE html>
<head>
<title>Food Recipe System | About Us</title>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="form-w3layouts">
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="card-header">
                            <?php _e('Update About us'); ?>
                        </header>
                        <div class="card-body">
                            <div class="form">
                                <?php if($msg): ?><p style="font-size:16px; color:<?php echo strpos($msg,'updated')!==false||strpos($msg,'cập nhật')!==false?'green':'red'; ?>" align="center"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>
                                <form class="cmxform form-horizontal" method="post" action="">
                                    <?php
                                    $ret=mysqli_query($con,"select * from pages where PageType='aboutus'");
                                    while ($row=mysqli_fetch_array($ret)) {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3"><?php _e('Page Title'); ?></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" id="pagetitle" name="pagetitle" type="text" required value="<?php echo htmlspecialchars($row['PageTitle']);?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3"><?php _e('Page Description'); ?></label>
                                        <div class="col-lg-6">
                                            <textarea class="form-control" id="pagedes" name="pagedes" required><?php echo htmlspecialchars($row['PageDescription']);?></textarea>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <div class="col-lg-offset-3 col-lg-6">
                                            <p style="text-align: center;"><button class="btn btn-primary" type="submit" name="submit"><?php _e('Update'); ?></button></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
    <?php include_once('includes/footer.php');?>
</section>
</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
<?php } ?>
