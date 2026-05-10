<?php
// AI Helper - OpenAI API Integration
require_once __DIR__ . '/config.php';

// Process raw ingredients through OpenAI API (Returns array or false)
function processIngredients(array $rawIngredients): array|false {
    // Filter out empty entries
    $rawIngredients = array_values(array_filter($rawIngredients, function($v) {
        return trim($v) !== '';
    }));

    if (empty($rawIngredients)) {
        return false;
    }

    $ingredientList = implode("\n", array_map(function($item, $idx) {
        return ($idx + 1) . ". " . trim($item);
    }, $rawIngredients, array_keys($rawIngredients)));

    $prompt = <<<PROMPT
You are a professional nutritionist and food expert. Analyze the following list of recipe ingredients.

For each ingredient, you must:
1. **Separate** the quantity/unit from the ingredient name.
2. **Normalize the ingredient name** to a standard English canonical name (e.g., "trứng" → "egg", "bột mì" → "flour", "coriander leaves" → "cilantro", "ngò rí" → "cilantro").
   - ALWAYS output the canonical name in English regardless of input language.
   - If the input is Vietnamese, translate and normalize to standard English.
   - If the input is English, normalize to standard English.
3. **Provide the Vietnamese name** for the ingredient in the `nameVi` field (e.g., "egg" → "trứng", "flour" → "bột mì").
4. **Convert the quantity to grams (for solids) or milliliters (for liquids)**.
   - Use standard cooking conversions (1 cup = 240ml, 1 tbsp = 15ml, 1 tsp = 5ml, etc.)
   - For items counted by pieces (e.g., "3 eggs"), estimate the weight in grams.
5. **Provide calories per 100g** for each ingredient.
6. **Split multiple ingredients**: If a single line contains multiple ingredients (e.g. separated by commas, "and", "với"), split them into separate output objects.

INGREDIENTS LIST:
{$ingredientList}

RESPOND WITH ONLY VALID JSON (no markdown, no code blocks, no explanation), using this exact structure:
{
  "totalCalories": <integer>,
  "ingredients": [
    {
      "original": "<original input string>",
      "canonicalName": "<normalized English name, e.g. 'egg', 'flour', 'chicken breast'>",
      "nameVi": "<Vietnamese name, e.g. 'trứng', 'bột mì', 'ức gà'>",
      "quantityOriginal": "<original quantity+unit, e.g. '2 cups'>",
      "quantityGrams": <number in grams or ml>,
      "caloriesPer100g": <integer>,
      "standardUnit": "<'g' for solids, 'ml' for liquids>"
    }
  ]
}
PROMPT;

    $result = callAI_API($prompt);
    
    if ($result === false) {
        return false;
    }

    return $result;
}

// Call OpenAI API
function callAI_API(string $prompt): array|false {
    $payload = json_encode([
        'model' => OPENAI_MODEL,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.1,
        'response_format' => ['type' => 'json_object']
    ]);

    $ch = curl_init(OPENAI_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("OpenAI API cURL error: " . $curlError);
        return false;
    }

    if ($httpCode !== 200) {
        error_log("OpenAI API HTTP error {$httpCode}: " . $response);
        return false;
    }

    $data = json_decode($response, true);
    
    if (!$data || !isset($data['choices'][0]['message']['content'])) {
        error_log("OpenAI API unexpected response structure: " . $response);
        return false;
    }

    $text = $data['choices'][0]['message']['content'];
    
    $parsed = json_decode($text, true);
    
    if ($parsed === null) {
        error_log("OpenAI API JSON parse error. Raw text: " . $text);
        return false;
    }

    // Validate structure
    if (!isset($parsed['ingredients']) || !is_array($parsed['ingredients'])) {
        error_log("OpenAI API response missing ingredients field: " . $text);
        return false;
    }

    // Force calculate total calories in PHP (AI math can be unreliable)
    $totalCalories = 0;
    foreach ($parsed['ingredients'] as &$ing) {
        $qty = floatval($ing['quantityGrams'] ?? 0);
        $cal = intval($ing['caloriesPer100g'] ?? 0);
        $totalCalories += ($qty / 100.0) * $cal;
    }
    $parsed['totalCalories'] = round($totalCalories);

    return $parsed;
}

// Save processed ingredients to database
function saveIngredientsToDb(mysqli $con, int $recipeId, array $aiResult): bool {
    // First, delete any existing ingredient mappings for this recipe
    $stmt = $con->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->close();

    foreach ($aiResult['ingredients'] as $ing) {
        $canonicalName = trim($ing['canonicalName']);
        $nameVi = trim($ing['nameVi'] ?? '');
        $caloriesPer100g = intval($ing['caloriesPer100g'] ?? 0);
        $standardUnit = $ing['standardUnit'] ?? 'g';
        $quantityOriginal = $ing['quantityOriginal'] ?? '';
        $quantityGrams = floatval($ing['quantityGrams'] ?? 0);

        // Check if ingredient already exists — match by English name, Vietnamese name,
        // or old rows that stored a Vietnamese name directly in the `name` column
        $stmt = $con->prepare(
            "SELECT id FROM ingredients
             WHERE LOWER(name) = LOWER(?)
                OR LOWER(name) = LOWER(?)
                OR (name_vi IS NOT NULL AND LOWER(name_vi) = LOWER(?))
             LIMIT 1"
        );
        $stmt->bind_param("sss", $canonicalName, $nameVi, $nameVi);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $ingredientId = $row['id'];
            // Update calorie info and name_vi if we have better data
            $updateStmt = $con->prepare(
                "UPDATE ingredients SET
                    caloriesPer100g = IF(caloriesPer100g = 0 AND ? > 0, ?, caloriesPer100g),
                    standardUnit    = IF(? = 'ml', 'ml', standardUnit),
                    name_vi         = IF(name_vi IS NULL AND ? != '', ?, name_vi)
                 WHERE id = ?"
            );
            $updateStmt->bind_param("iisssi", $caloriesPer100g, $caloriesPer100g, $standardUnit, $nameVi, $nameVi, $ingredientId);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert new ingredient
            $insertStmt = $con->prepare("INSERT INTO ingredients (name, name_vi, caloriesPer100g, standardUnit) VALUES (?, ?, ?, ?)");
            $nameViParam = $nameVi !== '' ? $nameVi : null;
            $insertStmt->bind_param("ssis", $canonicalName, $nameViParam, $caloriesPer100g, $standardUnit);
            $insertStmt->execute();
            $ingredientId = $con->insert_id;
            $insertStmt->close();
        }
        $stmt->close();

        // Insert recipe-ingredient mapping
        $mapStmt = $con->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantityOriginal, quantityGrams) VALUES (?, ?, ?, ?)");
        $mapStmt->bind_param("iisd", $recipeId, $ingredientId, $quantityOriginal, $quantityGrams);
        $mapStmt->execute();
        $mapStmt->close();
    }

    // Update total calories on the recipe
    $totalCalories = intval($aiResult['totalCalories'] ?? 0);
    $calStmt = $con->prepare("UPDATE recipes SET totalCalories = ? WHERE id = ?");
    $calStmt->bind_param("ii", $totalCalories, $recipeId);
    $calStmt->execute();
    $calStmt->close();

    return true;
}

// Load ingredients for a recipe from database
function loadRecipeIngredients(mysqli $con, int $recipeId): array {
    $stmt = $con->prepare(
        "SELECT i.name, i.name_vi, ri.quantityOriginal, ri.quantityGrams, i.caloriesPer100g, i.standardUnit
         FROM recipe_ingredients ri
         JOIN ingredients i ON ri.ingredient_id = i.id
         WHERE ri.recipe_id = ?
         ORDER BY ri.id ASC"
    );
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }
    $stmt->close();
    
    return $ingredients;
}
