<?php
header('Content-Type: application/json; charset=utf-8');
include('../includes/dbconnection.php');

$raw = isset($_GET['recipe_ids']) ? $_GET['recipe_ids'] : '';
$ids = array_filter(array_map('intval', explode(',', $raw)), fn($v) => $v > 0);

if (empty($ids)) {
    echo json_encode(['recipes' => [], 'ingredients' => [], 'total_calories' => 0]);
    exit;
}

$placeholders = implode(',', $ids);

// Fetch recipe titles for the summary
$recipeRows = [];
$rStmt = $con->prepare(
    "SELECT id, recipeTitle FROM recipes WHERE id IN ($placeholders)"
);
$rStmt->execute();
$rResult = $rStmt->get_result();
while ($r = $rResult->fetch_assoc()) {
    $recipeRows[] = ['id' => intval($r['id']), 'title' => $r['recipeTitle']];
}
$rStmt->close();

// Aggregate ingredients
$iStmt = $con->prepare(
    "SELECT i.id AS ingredient_id, i.name, i.name_vi, i.standardUnit, i.caloriesPer100g,
            SUM(ri.quantityGrams) AS total_grams
     FROM recipe_ingredients ri
     JOIN ingredients i ON ri.ingredient_id = i.id
     WHERE ri.recipe_id IN ($placeholders)
     GROUP BY i.id
     ORDER BY i.name ASC"
);
$iStmt->execute();
$iResult = $iStmt->get_result();

$ingredients = [];
$totalCalories = 0;
while ($row = $iResult->fetch_assoc()) {
    $totalGrams = floatval($row['total_grams']);
    $cal = intval($row['caloriesPer100g']);
    $totalCalories += ($totalGrams / 100.0) * $cal;

    $unit = $row['standardUnit'] ?: 'g';
    $display = round($totalGrams, 1) . $unit;

    $ingredients[] = [
        'ingredient_id' => intval($row['ingredient_id']),
        'name'          => $row['name'],
        'name_vi'       => $row['name_vi'],
        'total_grams'   => $totalGrams,
        'standard_unit' => $unit,
        'display'       => $display,
    ];
}
$iStmt->close();

echo json_encode([
    'recipes'       => $recipeRows,
    'ingredients'   => $ingredients,
    'total_calories'=> round($totalCalories),
], JSON_UNESCAPED_UNICODE);
