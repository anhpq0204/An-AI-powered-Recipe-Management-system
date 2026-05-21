<?php
if (!empty($_SESSION['frs_toast_msg'])) {
    $frsToastMsg = $_SESSION['frs_toast_msg'];
    $frsToastType = $_SESSION['frs_toast_type'] ?? 'success';
    unset($_SESSION['frs_toast_msg'], $_SESSION['frs_toast_type']);
}
?>
<div id="frsToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999"></div>
<?php if (!empty($frsToastMsg)): ?>
<script>window._frsToast = <?php echo json_encode(['msg' => $frsToastMsg, 'type' => $frsToastType ?? 'success']); ?>;</script>
<?php endif; ?>
<div class="footer">
    <div class="wthree-copyright">
        <p>&copy; <?php echo date('Y'); ?> Food Recipe System - User Panel. All rights reserved.</p>
    </div>
</div>
