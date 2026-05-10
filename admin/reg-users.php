<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recipe System | <?php _e('Registered Users'); ?></title>
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
                    <?php _e('Registered User Details'); ?>
                </div>
                <div>
                    <table class="table" ui-jq="footable" ui-options='{"paging":{"enabled":true},"filtering":{"enabled":true},"sorting":{"enabled":true}}'>
                        <thead>
                            <tr>
                                <th data-breakpoints="xs">S.NO</th>
                                <th><?php _e('Full Name'); ?></th>
                                <th><?php _e('Mobile Number'); ?></th>
                                <th><?php _e('Email'); ?></th>
                                <th><?php _e('Registration Date'); ?></th>
                                <th><?php _e('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ret = mysqli_query($con, "SELECT * FROM users");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                                <td><?php echo htmlspecialchars($row['MobileNumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                <td><?php echo htmlspecialchars($row['RegDate']); ?></td>
                                <td><a href="user-recipes.php?uid=<?php echo intval($row['ID']); ?>&uname=<?php echo htmlspecialchars($row['FullName']); ?>" class="btn btn-primary btn-sm" target="blank"><?php _e('Recipes'); ?></a></td>
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
