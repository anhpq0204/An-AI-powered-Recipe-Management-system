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
$planName = trim($_POST['plan_name'] ?? '');
$rawIds = $_POST['recipe_ids'] ?? '';
$ids = array_filter(array_map('intval', explode(',', $rawIds)), fn($v) => $v > 0);

if ($planName === '') {
    echo json_encode(['success' => false, 'error' => 'Plan name is required']);
    exit;
}
if (empty($ids)) {
    echo json_encode(['success' => false, 'error' => 'Select at least one recipe']);
    exit;
}

// Insert plan
$stmt = $con->prepare("INSERT INTO meal_plans (user_id, plan_name) VALUES (?, ?)");
$stmt->bind_param("is", $uid, $planName);
$stmt->execute();
$planId = $con->insert_id;
$stmt->close();

// Insert recipe mappings
$mapStmt = $con->prepare("INSERT IGNORE INTO meal_plan_recipes (plan_id, recipe_id) VALUES (?, ?)");
foreach ($ids as $recipeId) {
    $mapStmt->bind_param("ii", $planId, $recipeId);
    $mapStmt->execute();
}
$mapStmt->close();

echo json_encode(['success' => true, 'plan_id' => $planId]);
