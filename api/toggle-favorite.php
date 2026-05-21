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
$recipeId = intval($_POST['recipe_id'] ?? 0);

if ($recipeId <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid recipe_id']);
    exit;
}

// Check current state
$stmt = $con->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
$stmt->bind_param("ii", $uid, $recipeId);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($exists) {
    $stmt = $con->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $uid, $recipeId);
    $stmt->execute();
    $stmt->close();
    $isFavorited = false;
} else {
    $stmt = $con->prepare("INSERT IGNORE INTO favorites (user_id, recipe_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $uid, $recipeId);
    $stmt->execute();
    $stmt->close();
    $isFavorited = true;
}

// Return updated count
$stmt = $con->prepare("SELECT COUNT(*) AS cnt FROM favorites WHERE recipe_id = ?");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode(['success' => true, 'is_favorited' => $isFavorited, 'count' => intval($row['cnt'])]);
