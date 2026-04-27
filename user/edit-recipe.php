<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');
include('../includes/ai-helper.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['frsuid'];
$recipeid = isset($_GET['recipeid']) ? intval($_GET['recipeid']) : 0;
$msg = "";
$msgType = "";

// ── XỬ LÝ FORM UPDATE ────────────────────────────────────
if (isset($_POST['update'])) {
    $recipetitle = mysqli_real_escape_string($con, $_POST['recipetitle']);
    $recipeprep = mysqli_real_escape_string($con, $_POST['recipeprep']);
    $recipecooktime = mysqli_real_escape_string($con, $_POST['recipecooktime']);
    $yields = mysqli_real_escape_string($con, $_POST['yields']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    // Xử lý Ảnh
    $picdata = $_FILES["images"]["name"];
    if ($picdata == '') {
        $foodpic = $_POST["image"]; // Use old picture
    } else {
        $extension = strtolower(substr($picdata, strrpos($picdata, '.')));
        $allowed_extensions = array('.jpg', '.jpeg', '.png', '.gif');
        
        if (!in_array($extension, $allowed_extensions)) {
            echo "<script>alert('Invalid format. Only jpg / jpeg/ png /gif format allowed');</script>";
            $foodpic = $_POST["image"];
        } else {
            $foodpic = md5($picdata . time()) . $extension;
            move_uploaded_file($_FILES["images"]["tmp_name"], "images/" . $foodpic);
        }
    }

    // Update recipe (không có recipeIngredients nữa)
    $query = mysqli_query($con, "UPDATE recipes SET recipeTitle='$recipetitle', recipePrepTime='$recipeprep', recipeCookTime='$recipecooktime', recipeYields='$yields', recipeDescription='$description', recipePicture='$foodpic' WHERE userId='$uid' AND id='$recipeid'");

    if ($query) {
        // Xử lý ingredients qua AI
        $fitem = isset($_POST["fitem"]) ? $_POST["fitem"] : [];
        $fitem = array_filter($fitem, function($value) { return trim($value) !== ''; });
        $fitem = array_values($fitem);
        
        if (!empty($fitem)) {
            $aiResult = processIngredients($fitem);
            
            if ($aiResult !== false) {
                saveIngredientsToDb($con, $recipeid, $aiResult);
                $msg = "Recipe updated successfully! AI calculated " . $aiResult['totalCalories'] . " calories.";
                $msgType = "success";
            } else {
                // AI failed - fallback: xóa mapping cũ, lưu thủ công
                mysqli_query($con, "DELETE FROM recipe_ingredients WHERE recipe_id = $recipeid");
                foreach ($fitem as $item) {
                    $item = trim($item);
                    if (empty($item)) continue;
                    
                    $itemEscaped = mysqli_real_escape_string($con, $item);
                    $checkResult = mysqli_query($con, "SELECT id FROM ingredients WHERE LOWER(name) = LOWER('$itemEscaped')");
                    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                        $ingRow = mysqli_fetch_assoc($checkResult);
                        $ingredientId = $ingRow['id'];
                    } else {
                        mysqli_query($con, "INSERT INTO ingredients (name) VALUES ('$itemEscaped')");
                        $ingredientId = mysqli_insert_id($con);
                    }
                    mysqli_query($con, "INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantityOriginal) VALUES ($recipeid, $ingredientId, '$itemEscaped')");
                }
                $msg = "Recipe updated successfully (AI unavailable - calories not recalculated).";
                $msgType = "success";
            }
        } else {
            $msg = "Recipe updated (no ingredients provided).";
            $msgType = "success";
        }
    } else {
        $msg = "Something went wrong. Please try again.";
        $msgType = "danger";
    }
}

// ── LẤY DỮ LIỆU ĐỂ HIỂN THỊ ──────────────────────────────
$ret = mysqli_query($con, "SELECT * FROM recipes WHERE userId='$uid' AND id='$recipeid'");
$recipeData = mysqli_fetch_array($ret);

// Load ingredients từ DB (bảng mới)
$ingredients = [];
if ($recipeData) {
    $ingredients = loadRecipeIngredients($con, $recipeid);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | Edit Recipe</title>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var i = 1;
        var addBtn = document.getElementById('add');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                i++;
                var table = document.getElementById('dynamic_field');
                if (table) {
                    var newRow = '<tr id="row' + i + '"><td><input type="text" name="fitem[]" placeholder="Enter Recipe Ingredient" class="form-control" autocomplete="off" /></td><td style="width: 100px;"><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove btn-sm">X</button></td></tr>';
                    table.insertAdjacentHTML('beforeend', newRow);
                }
            });
        }
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn_remove')) {
                var buttonId = e.target.id;
                var row = document.getElementById('row' + buttonId);
                if (row) row.remove();
            }
        });
    });
    </script>
</head>
<body>
<section id="container">

<!-- Header -->
<?php include_once('includes/header.php');?>
<!-- Sidebar -->
<?php include_once('includes/sidebar.php');?>

<!-- Main Content -->
<section id="main-content">
    <section class="wrapper">
        <h1 class="user-page-title">
            Edit Recipe
            <small>Update your recipe details</small>
        </h1>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <header class="card-header">
                        Recipe Details
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($recipeData): ?>
                        <form class="form-horizontal" method="post" enctype="multipart/form-data">

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Recipe Title</label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipetitle" value="<?php echo htmlspecialchars($recipeData['recipeTitle']); ?>" type="text" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Prep Time <small class="text-muted">(mins)</small></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipeprep" value="<?php echo htmlspecialchars($recipeData['recipePrepTime']); ?>" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Cook Time <small class="text-muted">(mins)</small></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipecooktime" value="<?php echo htmlspecialchars($recipeData['recipeCookTime']); ?>" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Yields <small class="text-muted">(e.g. 8 Servings)</small></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="yields" value="<?php echo htmlspecialchars($recipeData['recipeYields']); ?>" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Ingredients</label>
                                <div class="col-sm-6">
                                    <table class="table table-bordered mb-0" id="dynamic_field">
                                        <?php 
                                        $idx = 0;
                                        foreach($ingredients as $ing): 
                                            // Hiển thị dạng: "quantityOriginal ingredientName" hoặc chỉ name
                                            $displayValue = '';
                                            if (!empty($ing['quantityOriginal'])) {
                                                $displayValue = $ing['quantityOriginal'] . ' ' . $ing['name'];
                                            } else {
                                                $displayValue = $ing['name'];
                                            }
                                        ?>
                                        <tr id="row_exist_<?php echo $idx; ?>">
                                            <td><input type="text" name="fitem[]" value="<?php echo htmlspecialchars($displayValue); ?>" class="form-control" autocomplete="off" required/></td>
                                            <td style="width: 100px;">
                                                <?php if($idx == 0): ?>
                                                    <button type="button" name="add" id="add" class="btn btn-success btn-sm">Add More</button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-danger btn_remove btn-sm" id="_exist_<?php echo $idx; ?>">X</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php $idx++; endforeach; 
                                        // Mặc định luôn có ít nhất 1 dòng
                                        if($idx == 0): ?>
                                        <tr>
                                            <td><input type="text" name="fitem[]" placeholder="Enter Recipe Ingredient" class="form-control" autocomplete="off" required/></td>
                                            <td style="width: 100px;"><button type="button" name="add" id="add" class="btn btn-success btn-sm">Add More</button></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                    <small class="text-muted mt-1 d-block">💡 AI will normalize ingredient names and recalculate calories on update.</small>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Description</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="description" rows="8" required><?php echo htmlspecialchars($recipeData['recipeDescription']); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold">Current Picture</label>
                                <div class="col-sm-6">
                                    <img src="images/<?php echo htmlspecialchars($recipeData['recipePicture']); ?>" width="200" class="img-thumbnail mb-2 d-block"><br>
                                    <input type="hidden" name="image" value="<?php echo htmlspecialchars($recipeData['recipePicture']); ?>">
                                    <input type="file" class="form-control" name="images" id="images" accept=".jpg,.jpeg,.png,.gif">
                                    <small class="text-muted">Leave empty if you don't want to change the picture.</small>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <div class="form-group row">
                                <div class="col-sm-6 offset-sm-3">
                                    <button class="btn btn-primary px-4" type="submit" name="update">Update Recipe</button>
                                </div>
                            </div>

                        </form>
                        <?php else: ?>
                            <p class="text-danger text-center">Recipe not found or you don't have permission to edit it.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
