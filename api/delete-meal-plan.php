<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'unauthorized']);
    exit;
}

$uid = intval($_SESSION['frsuid']);
$planId = intval($_POST['plan_id'] ?? 0);

if ($planId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid plan']);
    exit;
}

// Delete recipe mappings first
$stmt = $con->prepare("DELETE mpr FROM meal_plan_recipes mpr
    INNER JOIN meal_plans mp ON mpr.plan_id = mp.id
    WHERE mp.id = ? AND mp.user_id = ?");
$stmt->bind_param("ii", $planId, $uid);
$stmt->execute();
$stmt->close();

// Delete plan (only if owned by this user)
$stmt2 = $con->prepare("DELETE FROM meal_plans WHERE id = ? AND user_id = ?");
$stmt2->bind_param("ii", $planId, $uid);
$stmt2->execute();
$affected = $stmt2->affected_rows;
$stmt2->close();

if ($affected > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Plan not found']);
}
