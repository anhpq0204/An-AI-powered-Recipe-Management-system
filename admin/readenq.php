<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<head>
<title>Food Recipe System | Read Enquiry</title>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="table-agile-info">
            <div class="card">
                <div class="card-header"><?php _e('Read Enquiry Details'); ?></div>
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th><?php _e('Name'); ?></th>
                                <th><?php _e('Email'); ?></th>
                                <th><?php _e('Subject'); ?></th>
                                <th><?php _e('Enquiry Date'); ?></th>
                                <th><?php _e('Action'); ?></th>
                            </tr>
                        </thead>
                        <?php
                        $ret=mysqli_query($con,"select * from enquiries where adminRemark!=''");
                        $cnt=1;
                        while ($row=mysqli_fetch_array($ret)) {
                        ?>
                        <tbody>
                            <tr class="gradeX">
                                <td><?php echo $cnt;?></td>
                                <td><?php echo htmlspecialchars($row['userName']);?></td>
                                <td><?php echo htmlspecialchars($row['userEmail']);?></td>
                                <td><?php echo htmlspecialchars($row['subject']);?></td>
                                <td><span class="badge badge-primary"><?php echo htmlspecialchars($row['postingDate']);?></span></td>
                                <td><a href="view-enquiry.php?enqid=<?php echo intval($row['id']);?>" class="btn btn-primary"><?php _e('View'); ?></a></td>
                            </tr>
                        <?php $cnt++; } ?>
                        </tbody>
                    </table>
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
