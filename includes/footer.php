<?php
if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['frs_toast_msg'])) {
    $frsToastMsg = $_SESSION['frs_toast_msg'];
    $frsToastType = $_SESSION['frs_toast_type'] ?? 'success';
    unset($_SESSION['frs_toast_msg'], $_SESSION['frs_toast_type']);
}
?>
<div id="frsToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999"></div>
<?php if (!empty($frsToastMsg)): ?>
<script>window._frsToast = <?php echo json_encode(['msg' => $frsToastMsg, 'type' => $frsToastType ?? 'success']); ?>;</script>
<?php endif; ?>
<footer class="footer-area">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-12 h-100 d-flex flex-wrap align-items-center justify-content-between">
                <div class="footer-social-info text-right">
                    <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                    <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                    <a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                </div>
                <div class="footer-logo">
                    <a href="index.php">FRS</a>
                </div>
                <p>&copy; <?php echo date('Y'); ?> Food Recipe System</p>
            </div>
        </div>
    </div>
</footer>