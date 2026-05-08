<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['frsuid'];

// ── LẤY DANH SÁCH BÌNH LUẬN ──────────────────────────────
$queryStr = "SELECT recipes.recipeTitle, recipes.id as rid, comments.* 
             FROM comments 
             JOIN recipes ON recipes.id = comments.recipeId 
             WHERE recipes.userId = '$uid'  
             ORDER BY comments.id DESC";
$ret = mysqli_query($con, $queryStr);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | All Comments</title>
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
        <h1 class="user-page-title">
            All Comments
            <small>Manage comments on your recipes</small>
        </h1>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <header class="card-header">
                        All Comments Overview
                    </header>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Recipe Title</th>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Comment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $cnt = 1;
                                if (mysqli_num_rows($ret) > 0) {
                                    while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                    <tr>
                                        <td><?php echo $cnt;?></td>
                                        <td><a href="edit-recipe.php?recipeid=<?php echo intval($row['rid']);?>" target="_blank" class="text-primary text-decoration-none fw-bold"><?php echo htmlspecialchars($row['recipeTitle']);?></a></td>
                                        <td><?php echo htmlspecialchars($row['userName']);?></td>
                                        <td><?php echo htmlspecialchars($row['userEmail']);?></td>
                                        <td><?php echo htmlspecialchars($row['commentMessage']);?></td>
                                        <td>
                                            <?php $status = $row['status']; ?>
                                            <?php if($status == ''): ?>
                                                <button class="btn btn-warning btn-sm">Waiting for Approval</button>
                                            <?php elseif($status == '0'): ?>
                                                <button class="btn btn-danger btn-sm">Rejected</button>
                                            <?php else: ?>
                                                <button class="btn btn-success btn-sm">Approved</button>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['postingDate']);?></td>
                                    </tr>
                                <?php 
                                        $cnt++;
                                    }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No comments found.</td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>    
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
