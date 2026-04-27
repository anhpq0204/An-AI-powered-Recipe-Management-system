<?php require_once('../includes/session.php');
include('../includes/dbconnection.php');
include('../includes/ai-helper.php');

if (!isset($_SESSION['frsaid']) || strlen($_SESSION['frsaid']) == 0) {
    header('location:logout.php');
    exit;
}

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

    $picdata = $_FILES["images"]["name"];
    if ($picdata == '') {
        $foodpic = $_POST["image"];
    } else {
        $extension = strtolower(substr($picdata, strrpos($picdata, '.')));
        $allowed_extensions = array('.jpg', '.jpeg', '.png', '.gif');
        
        if (!in_array($extension, $allowed_extensions)) {
            $msg = "Invalid format. Only jpg / jpeg/ png /gif format allowed.";
            $msgType = "danger";
            $foodpic = $_POST["image"];
        } else {
            $foodpic = md5($picdata . time()) . $extension;
            move_uploaded_file($_FILES["images"]["tmp_name"], "../user/images/" . $foodpic);
        }
    }

    // Update recipe (không có recipeIngredients nữa)
    $query = mysqli_query($con, "UPDATE recipes SET recipeTitle='$recipetitle', recipePrepTime='$recipeprep', recipeCookTime='$recipecooktime', recipeYields='$yields', recipeDescription='$description', recipePicture='$foodpic' WHERE id='$recipeid'");

    if ($query) {
        // Xử lý ingredients qua AI
        $fitem = isset($_POST["fitem"]) ? $_POST["fitem"] : [];
        $fitem = array_filter($fitem, function($value) { return trim($value) !== ''; });
        $fitem = array_values($fitem);
        
        if (!empty($fitem)) {
            $aiResult = processIngredients($fitem);
            
            if ($aiResult !== false) {
                saveIngredientsToDb($con, $recipeid, $aiResult);
                $msg = "Recipe updated! AI calculated " . $aiResult['totalCalories'] . " calories.";
                $msgType = "success";
            } else {
                // AI failed - fallback
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
                $msg = "Recipe updated (AI unavailable - calories not recalculated).";
                $msgType = "success";
            }
        } else {
            $msg = "Recipe updated.";
            $msgType = "success";
        }
    } else {
        $msg = "Something went wrong. Please try again.";
        $msgType = "danger";
    }
}

// ── LẤY DỮ LIỆU ──────────────────────────────────────────
$ret = mysqli_query($con, "SELECT * FROM recipes WHERE id='$recipeid'");
$recipeData = mysqli_fetch_array($ret);

// Load ingredients từ bảng mới
$ingredients = [];
if ($recipeData) {
    $ingredients = loadRecipeIngredients($con, $recipeid);
}
?>
<!DOCTYPE html>
<head>
<title>Food Recipe System | Edit Recipe Details</title>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var i = 1;
    var addBtn = document.getElementById('add');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            i++;
            var table = document.getElementById('dynamic_field');
            if (table) {
                table.insertAdjacentHTML('beforeend',
                    '<tr id="row' + i + '"><td><input type="text" name="fitem[]" placeholder="Enter Recipe Ingredient" class="form-control name_list" autocomplete="off" /></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>'
                );
            }
        });
    }
    // Event delegation for remove buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn_remove')) {
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
<!--header start-->
<?php include_once('includes/header.php');?>
<!--header end-->
<!--sidebar start-->
<?php include_once('includes/sidebar.php');?>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
	<div class="form-w3layouts">
    
        <div class="row">
        <div class="col-lg-12">
        <section class="panel">
            <header class="card-header">
                Edit Recipe Details
            </header>
            <div class="card-body">

                <?php if ($msg): ?>
                    <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
      <?php if ($recipeData): ?>

                <form class="form-horizontal bucket-form" method="post" enctype="multipart/form-data">

                   <div class="form-group">
                        <label class="col-sm-3 control-label">Recipe Title </label>
                        <div class="col-sm-6">
                            <input class="form-control" id="recipetitle" name="recipetitle" value="<?php echo htmlspecialchars($recipeData['recipeTitle']);?>" type="text" required="true">
                        </div>
                    </div>

                   <div class="form-group">
                        <label class="col-sm-3 control-label">Recipe Preperation Time <small>(in minutes)</small></label>
                        <div class="col-sm-6">
                            <input class="form-control" id="recipeprep" name="recipeprep" value="<?php echo htmlspecialchars($recipeData['recipePrepTime']);?>" pattern="[0-9]+" type="text" required="true" title="Numbers only">
                        </div>
                    </div>


         <div class="form-group">
                        <label class="col-sm-3 control-label">Recipe Cook Time <small>(in minutes)</small></label>
                        <div class="col-sm-6">
                            <input class="form-control" id="recipecooktime" name="recipecooktime" value="<?php echo htmlspecialchars($recipeData['recipeCookTime']);?>"  pattern="[0-9]+" type="text" required="true" title="Numbers only">
                        </div>
                    </div>


        <div class="form-group">
                        <label class="col-sm-3 control-label">Yields <small>(Eg: 8 Servings)</small></label>
                        <div class="col-sm-6">
                            <input class="form-control" id="yields" name="yields" value="<?php echo htmlspecialchars($recipeData['recipeYields']);?>"  pattern="[0-9]+" type="text" required="true" title="Numbers only">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label">Recipe Ingredients</label>
                        <div class="col-sm-6">
                            <table class="table table-bordered" id="dynamic_field">
<?php 
$idx = 0;
foreach($ingredients as $ing): 
    $displayValue = '';
    if (!empty($ing['quantityOriginal'])) {
        $displayValue = $ing['quantityOriginal'] . ' ' . $ing['name'];
    } else {
        $displayValue = $ing['name'];
    }
?>
<tr id="row_exist_<?php echo $idx; ?>">
<td><input type="text" name="fitem[]" value="<?php echo htmlspecialchars($displayValue); ?>" class="form-control name_list" autocomplete="off" /></td>
<td>
    <?php if($idx == 0): ?>
        <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
    <?php else: ?>
        <button type="button" class="btn btn-danger btn_remove" id="_exist_<?php echo $idx; ?>">X</button>
    <?php endif; ?>
</td>
</tr>
<?php $idx++; endforeach;
if ($idx == 0): ?>
<tr>
<td><input type="text" name="fitem[]" placeholder="Enter Recipe Ingredient" class="form-control name_list" autocomplete="off" /></td>
<td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
</tr>
<?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="description" name="description" rows="10" required><?php echo htmlspecialchars($recipeData['recipeDescription']);?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class=" col-sm-3 control-label">Pictures</label>
                                <div class="col-sm-6">
                                    <img src="../user/images/<?php echo htmlspecialchars($recipeData['recipePicture']);?>" width="300"><br /><br />
                                    <input type="hidden" name="image" id="image" value="<?php echo htmlspecialchars($recipeData['recipePicture']);?>">
                                    <input type="file" class="form-control" name="images" id="images">
                                </div>
                            </div>
                            <hr />
                            
                            <div class="form-group">
                                <div class="col-lg-offset-3 col-sm-6">
                                    <button class="btn btn-primary" type="submit" name="update">Update</button>
                                </div>
                            </div>
                        </form>
                        <?php else: ?>
                        <p class="text-danger text-center">Recipe not found.</p>
                        <?php endif; ?>
                    </div>
                </section>
                <!-- page end-->
            </div>
        </section>
        
        <!-- footer -->
        <?php include_once('includes/footer.php');?>
        <!-- / footer -->
    </section>

<!--main content end-->
</section>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
