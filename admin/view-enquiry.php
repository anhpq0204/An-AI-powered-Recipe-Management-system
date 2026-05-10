<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

if (isset($_POST['submit'])) {
    $aremark = $_POST['adminremark'];
    $eid = intval($_GET['enqid']);
    $query = mysqli_query($con, "UPDATE enquiries SET adminRemark='$aremark' WHERE id='$eid'");
    echo "<script>alert('" . addslashes(__('Remark saved successfully.')) . "');</script>";
    echo "<script type='text/javascript'> document.location = 'readenq.php'; </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('View Enquiry Details'); ?></title>
</head>
<body>
<section id="container">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<section id="main-content">
    <section class="wrapper">
        <div class="table-agile-info">
            <div class="card">
                <div class="card-header">
                    <?php _e('View Enquiry Details'); ?>
                </div>
                <div>
                <?php
                $eid = intval($_GET['enqid']);
                $ret = mysqli_query($con, "SELECT * FROM enquiries WHERE id=$eid");
                while ($row = mysqli_fetch_array($ret)) {
                ?>
                    <table class="table" ui-jq="footable" ui-options='{"paging":{"enabled":true},"filtering":{"enabled":true},"sorting":{"enabled":true}}'>
                        <tr>
                            <th style="font-size:15px;"><?php _e('Name'); ?></th>
                            <td><?php echo htmlspecialchars($row['userName']); ?></td>
                            <th style="font-size:15px;"><?php _e('Email'); ?></th>
                            <td><?php echo htmlspecialchars($row['userEmail']); ?></td>
                        </tr>
                        <tr>
                            <th style="font-size:15px;"><?php _e('Subject'); ?></th>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <th style="font-size:15px;"><?php _e('Enquiry Date'); ?></th>
                            <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                        </tr>
                        <tr>
                            <th style="font-size:15px;"><?php _e('Message'); ?></th>
                            <td colspan="4"><?php echo htmlspecialchars($row['commentMessage']); ?></td>
                        </tr>
                        <?php if ($row['adminRemark'] != ''): ?>
                        <tr>
                            <th style="font-size:15px;"><?php _e('Admin Remark'); ?></th>
                            <td colspan="4"><?php echo htmlspecialchars($row['adminRemark']); ?></td>
                        </tr>
                        <tr>
                            <th style="font-size:15px;"><?php _e('Admin Remark Date'); ?></th>
                            <td><?php echo htmlspecialchars($row['updationDate']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($row['adminRemark'] == ''): ?>
                        <form method="post">
                        <tr>
                            <th style="font-size:15px;"><?php _e('Admin Remark'); ?></th>
                            <td colspan="4">
                                <textarea class="form-control" name="adminremark" rows="5" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><input class="btn btn-primary" type="submit" name="submit" value="<?php echo htmlspecialchars(__('Update')); ?>"></td>
                        </tr>
                        </form>
                        <?php endif; ?>
                    </table>
                <?php } ?>
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
