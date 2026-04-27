<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

$adminid = $_SESSION['frsaid'];
$msg = "";
$msgType = "";

// ── XỬ LÝ FORM POST ──────────────────────────────────────
if (isset($_POST['submit'])) {
    $aname = mysqli_real_escape_string($con, $_POST['adminname']);
    $mobno = mysqli_real_escape_string($con, $_POST['contactnumber']);
    
    $query = mysqli_query($con, "UPDATE admins SET AdminName='$aname', MobileNumber='$mobno' WHERE ID='$adminid'");
    if ($query) {
        $msg = "Profile details updated successfully.";
        $msgType = "success";
    } else {
        $msg = "Something went wrong. Please try again.";
        $msgType = "danger";
    }
}

// ── LẤY DỮ LIỆU CHUẨN BỊ CHO VIEW ────────────────────────
$ret = mysqli_query($con, "SELECT * FROM admins WHERE ID='$adminid'");
$adminProfile = mysqli_fetch_array($ret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | Admin Profile</title>
</head>
<body>
<section id="container">

<!-- Header -->
<?php include_once('includes/header.php');?>
<!-- Sidebar -->
<?php include_once('includes/sidebar.php');?>

<!-- Main Content -->
<section id="main-content">
    <section class="wrapper">
        <h1 class="page-title" style="margin-bottom: 20px;">
            Profile Settings
            <small style="display: block; font-size: 14px; font-weight: normal; color: var(--text-muted);">Manage your administrator information</small>
        </h1>
        
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <header class="card-header">
                        Admin Profile
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($adminProfile): ?>
                        <form class="cmxform form-horizontal" method="post" action="">
                            <div class="form-group row mb-3">
                                <label for="adminname" class="control-label col-lg-4">Admin Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="adminname" name="adminname" type="text" value="<?php echo htmlspecialchars($adminProfile['AdminName']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="username" class="control-label col-lg-4">User Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control bg-light" id="username" name="username" type="text" value="<?php echo htmlspecialchars($adminProfile['UserName']); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="contactnumber" class="control-label col-lg-4">Contact Number</label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="contactnumber" name="contactnumber" type="text" value="<?php echo htmlspecialchars($adminProfile['MobileNumber']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="email" class="control-label col-lg-4">Email</label>
                                <div class="col-lg-8">
                                    <input class="form-control bg-light" id="email" name="email" type="email" value="<?php echo htmlspecialchars($adminProfile['Email']); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary px-4" type="submit" name="submit">Update Profile</button>
                                </div>
                            </div>
                        </form>
                        <?php else: ?>
                            <p class="text-danger text-center">Admin data not found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>    
</section>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="js/jquery.nicescroll.js"></script>
</body>
</html>