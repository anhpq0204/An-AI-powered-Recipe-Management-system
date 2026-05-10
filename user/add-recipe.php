<?php
require_once('../includes/lang.php');
require_once('../includes/session.php');
include('../includes/dbconnection.php');
include('../includes/ai-helper.php');

if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}

$userid = $_SESSION['frsuid'];
$msg = "";
$msgType = ""; // success or danger

// ── XỬ LÝ FORM THÊM RECIPE ───────────────────────────────
if (isset($_POST['submit'])) {
    $recipetitle = mysqli_real_escape_string($con, $_POST['recipetitle']);
    $recipeprep = mysqli_real_escape_string($con, $_POST['recipeprep']);
    $recipecooktime = mysqli_real_escape_string($con, $_POST['recipecooktime']);
    $yields = mysqli_real_escape_string($con, $_POST['yields']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // Xử lý File ảnh
    $pic = $_FILES["images"]["name"];
    if ($pic) {
        $extension = strtolower(substr($pic, strrpos($pic, '.')));
        $allowed_extensions = array('.jpg', '.jpeg', '.png', '.gif');

        if (!in_array($extension, $allowed_extensions)) {
            $msg = __('Invalid format. Only jpg / jpeg/ png /gif format allowed.');
            $msgType = "danger";
        } else {
            $foodpic = md5($pic . time()) . $extension;
            move_uploaded_file($_FILES["images"]["tmp_name"], "images/" . $foodpic);

            // Xử lý mảng Ingredients qua AI
            $fitem = isset($_POST["fitem"]) ? $_POST["fitem"] : [];
            $fitem = array_filter($fitem, function($value) { return trim($value) !== ''; });
            $fitem = array_values($fitem);

            if (empty($fitem)) {
                $msg = __('Please add at least one ingredient.');
                $msgType = "danger";
            } else {
                // 1. Insert recipe trước (không có ingredients column nữa)
                $query = mysqli_query($con, "INSERT INTO recipes(userId, recipeTitle, recipePrepTime, recipeCookTime, recipeYields, recipeDescription, recipePicture) VALUES ('$userid', '$recipetitle', '$recipeprep', '$recipecooktime', '$yields', '$description', '$foodpic')");

                if ($query) {
                    $recipeId = mysqli_insert_id($con);

                    // 2. Gọi AI để chuẩn hóa ingredients + tính calo
                    $aiResult = processIngredients($fitem);

                    if ($aiResult !== false) {
                        // 3. Lưu ingredients vào DB
                        saveIngredientsToDb($con, $recipeId, $aiResult);
                        $msg = sprintf(__('Recipe added successfully! AI calculated %d calories.'), $aiResult['totalCalories']);
                        $msgType = "success";
                    } else {
                        // AI failed - lưu ingredients thủ công (fallback không có calo)
                        foreach ($fitem as $item) {
                            $item = trim($item);
                            if (empty($item)) continue;

                            $itemEscaped = mysqli_real_escape_string($con, $item);
                            // Check if ingredient exists
                            $checkResult = mysqli_query($con, "SELECT id FROM ingredients WHERE LOWER(name) = LOWER('$itemEscaped')");
                            if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                                $ingRow = mysqli_fetch_assoc($checkResult);
                                $ingredientId = $ingRow['id'];
                            } else {
                                mysqli_query($con, "INSERT INTO ingredients (name) VALUES ('$itemEscaped')");
                                $ingredientId = mysqli_insert_id($con);
                            }

                            mysqli_query($con, "INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantityOriginal) VALUES ($recipeId, $ingredientId, '$itemEscaped')");
                        }

                        $msg = __('Recipe added successfully (AI unavailable - calories not calculated).');
                        $msgType = "success";
                    }
                } else {
                    $msg = __('Something went wrong. Please try again.');
                    $msgType = "danger";
                }
            }
        }
    } else {
        $msg = __('Please upload a recipe picture.');
        $msgType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Recipe System | <?php _e('Add Recipe'); ?></title>
    <script>
    var enterIngredientLabel = '<?php echo addslashes(__('Enter Recipe Ingredient')); ?>';
    document.addEventListener('DOMContentLoaded', function() {
        var i = 1;
        var addBtn = document.getElementById('add');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                i++;
                var table = document.getElementById('dynamic_field');
                if (table) {
                    var newRow = '<tr id="row' + i + '"><td><input type="text" name="fitem[]" placeholder="' + enterIngredientLabel + '" class="form-control" autocomplete="off" /></td><td style="width: 100px;"><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove btn-sm">X</button></td></tr>';
                    table.insertAdjacentHTML('beforeend', newRow);
                }
            });
        }
        // Remove button delegate
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
            <?php _e('Add New Recipe'); ?>
            <small><?php _e('Share your culinary creation with the community'); ?></small>
        </h1>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <header class="card-header">
                        <?php _e('Recipe Details'); ?>
                    </header>
                    <div class="card-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show popup-alert">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form class="form-horizontal" method="post" enctype="multipart/form-data">

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Recipe Title'); ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipetitle" type="text" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Prep Time'); ?> <small class="text-muted">(<?php _e('Mins'); ?>)</small></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipeprep" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Cook Time'); ?> <small class="text-muted">(<?php _e('Mins'); ?>)</small></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="recipecooktime" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Yields'); ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="yields" pattern="[0-9]+" type="text" required title="Numbers only">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Ingredients'); ?></label>
                                <div class="col-sm-6">
                                    <table class="table table-bordered mb-0" id="dynamic_field">
                                        <tr>
                                            <td><input type="text" name="fitem[]" placeholder="<?php echo htmlspecialchars(__('Enter Recipe Ingredient')); ?>" class="form-control" autocomplete="off" required /></td>
                                            <td style="width: 100px;"><button type="button" name="add" id="add" class="btn btn-success btn-sm"><?php _e('Add More'); ?></button></td>
                                        </tr>
                                    </table>
                                    <small class="text-muted mt-1 d-block"><?php _e('💡 AI will normalize ingredient names and calculate calories automatically.'); ?></small>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Description'); ?></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="description" rows="6" required></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end fw-bold"><?php _e('Recipe Picture'); ?></label>
                                <div class="col-sm-6">
                                    <input type="file" class="form-control" name="images" id="images" required accept=".jpg,.jpeg,.png,.gif">
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="form-group row">
                                <div class="col-sm-6 offset-sm-3">
                                    <button class="btn btn-primary px-4" type="submit" name="submit"><?php _e('Submit Recipe'); ?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
</body>
</html>
