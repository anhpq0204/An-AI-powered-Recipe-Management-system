<?php
header('Content-Type: application/json; charset=utf-8');
include('../includes/dbconnection.php');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q !== '') {
    $stmt = $con->prepare(
        "SELECT id, recipeTitle, recipePicture, recipePrepTime, recipeYields, totalCalories
         FROM recipes
         WHERE recipeTitle LIKE ?
         ORDER BY id DESC LIMIT 30"
    );
    $like = '%' . $q . '%';
    $stmt->bind_param("s", $like);
} else {
    $stmt = $con->prepare(
        "SELECT id, recipeTitle, recipePicture, recipePrepTime, recipeYields, totalCalories
         FROM recipes
         ORDER BY id DESC LIMIT 30"
    );
}

$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = [
        'id'           => intval($row['id']),
        'title'        => $row['recipeTitle'],
        'picture'      => $row['recipePicture'],
        'prepTime'     => intval($row['recipePrepTime']),
        'yields'       => intval($row['recipeYields']),
        'totalCalories'=> intval($row['totalCalories']),
    ];
}
$stmt->close();

echo json_encode($recipes, JSON_UNESCAPED_UNICODE);
