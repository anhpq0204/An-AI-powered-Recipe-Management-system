<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (empty($_SESSION['frsuid'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$uid      = intval($_SESSION['frsuid']);
$recipeId = intval($_POST['recipe_id'] ?? 0);
$rating   = intval($_POST['rating'] ?? 0);

if ($recipeId <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = $con->prepare(
    "INSERT INTO ratings (user_id, recipe_id, rating) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE rating = VALUES(rating)"
);
$stmt->bind_param("iii", $uid, $recipeId, $rating);
$stmt->execute();
$stmt->close();

$stmt = $con->prepare(
    "SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS cnt FROM ratings WHERE recipe_id = ?"
);
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode([
    'success'    => true,
    'avg_rating' => floatval($row['avg_rating']),
    'count'      => intval($row['cnt']),
    'user_rating'=> $rating,
]);
