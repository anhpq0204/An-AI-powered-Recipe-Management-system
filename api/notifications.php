<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');

if (empty($_SESSION['frsuid'])) {
    echo json_encode(['success' => false]);
    exit;
}

$uid    = intval($_SESSION['frsuid']);
$action = $_GET['action'] ?? 'list';

if ($action === 'count') {
    $stmt = $con->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $cnt = intval($stmt->get_result()->fetch_assoc()['cnt']);
    $stmt->close();
    echo json_encode(['count' => $cnt]);
    exit;
}

if ($action === 'read_all') {
    $stmt = $con->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
    exit;
}

// list
$stmt = $con->prepare(
    "SELECT id, type, message, recipe_id, is_read, created_at
     FROM notifications WHERE user_id = ?
     ORDER BY created_at DESC LIMIT 30"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
