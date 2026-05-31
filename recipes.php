<?php
require_once('includes/lang.php');
include('includes/dbconnection.php');
require_once('includes/session.php');

// Load favorite recipe IDs for logged-in user
$userFavIds = [];
if (!empty($_SESSION['frsuid'])) {
    $uid = intval($_SESSION['frsuid']);
    $favStmt = $con->prepare("SELECT recipe_id FROM favorites WHERE user_id = ?");
    $favStmt->bind_param("i", $uid);
    $favStmt->execute();
    $favRows = $favStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $favStmt->close();
    foreach ($favRows as $fr) { $userFavIds[$fr['recipe_id']] = true; }
}
$isLoggedIn = !empty($_SESSION['frsuid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Browse all recipes on Food Recipe System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | All Recipes</title>
    <link rel="icon" href="img/core-img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once('includes/topbar.php');?>
<?php include_once('includes/header.php');?>

    <!-- Page Header -->
    <section class="page-header-section" style="background-image: url(img/bg-img/breadcumb3.jpg);">
        <div class="page-header-overlay"></div>
        <div class="container">
            <div class="page-header-content">
                <span class="page-tag">Browse</span>
                <h1><?php _e('All Recipes'); ?></h1>
                <p><?php _e('Discover our complete collection of delicious recipes'); ?></p>
            </div>
        </div>
    </section>

    <!-- Recipes Grid -->
    <section class="featured-recipes-section">
        <div class="container">

            <!-- Ingredient Search Filter -->
            <div class="ingredient-filter-card" id="ingredientFilter">
                <h4><?php _e('🔍 Filter by Ingredients'); ?></h4>
                <div class="multi-select-dropdown">
                    <input type="text" class="multi-select-search" id="ingredientSearchInput" 
                           placeholder="Type to search ingredients..." autocomplete="off">
                    <div class="multi-select-list" id="ingredientList">
                        <div class="no-results">Type to search...</div>
                    </div>
                </div>
                <div class="selected-tags" id="selectedTags"></div>
                <button class="ingredient-search-btn" id="searchByIngredients" disabled>
                    🔍 Find Recipes
                </button>
            </div>

            <!-- Results area (for AJAX ingredient search results) -->
            <div id="ingredientSearchResults" style="display:none;">
                <div class="modern-section-heading" style="margin-bottom: 30px;">
                    <p id="ingredientResultsCount"></p>
                </div>
                <div class="recipes-grid" id="ingredientResultsGrid"></div>
                <div class="text-center" style="margin-top: 20px;">
                    <button class="btn-modern btn-outline-modern" id="clearIngredientSearch" 
                            style="color: var(--primary); border-color: var(--primary);">✕ Clear Filter</button>
                </div>
            </div>

            <!-- Default recipes listing -->
            <div id="defaultRecipesList">
<?php
$perPage = 9;
$page    = max(1, intval($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$totalStmt = $con->prepare("SELECT COUNT(*) AS cnt FROM recipes r WHERE r.status = 1");
$totalStmt->execute();
$totalRows = intval($totalStmt->get_result()->fetch_assoc()['cnt']);
$totalStmt->close();
$totalPages = (int) ceil($totalRows / $perPage);

$ret = mysqli_query($con, "SELECT r.recipeTitle, r.recipePicture, r.id, r.postingDate, r.recipePrepTime, r.recipeYields, r.totalCalories, r.status, u.FullName,
    (SELECT COUNT(*) FROM favorites WHERE recipe_id = r.id) AS fav_count,
    (SELECT ROUND(AVG(rating),1) FROM ratings WHERE recipe_id = r.id) AS avg_rating,
    (SELECT COUNT(*) FROM ratings WHERE recipe_id = r.id) AS rating_count
    FROM recipes r
    LEFT JOIN users u ON r.userId = u.ID
    WHERE r.status = 1
    ORDER BY r.id DESC
    LIMIT $perPage OFFSET $offset");
?>
            <div class="recipes-grid">
<?php while ($row = mysqli_fetch_array($ret)) {
    $isFav = isset($userFavIds[intval($row['id'])]);
    $favCount = intval($row['fav_count']);
    $avgRating = $row['avg_rating'] ? floatval($row['avg_rating']) : 0;
    $ratingCount = intval($row['rating_count']);
?>
                <div class="recipe-card">
                    <div class="recipe-card-image">
                        <img src="user/images/<?php echo htmlspecialchars($row['recipePicture']);?>" alt="<?php echo htmlspecialchars($row['recipeTitle']);?>" loading="lazy">
                        <div class="recipe-card-overlay">
                            <a href="recipe-details.php?rid=<?php echo intval($row['id']);?>" class="view-recipe-btn"><?php _e('View Recipe'); ?></a>
                        </div>
                    </div>
                    <div class="recipe-card-body">
                        <h5><a href="recipe-details.php?rid=<?php echo intval($row['id']);?>"><?php echo htmlspecialchars($row['recipeTitle']);?></a></h5>
                        <div class="recipe-meta">
                            <?php if($row['recipePrepTime']) { ?>
                            <span><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($row['recipePrepTime']);?> <?php _e('min'); ?></span>
                            <?php } ?>
                            <?php if($row['recipeYields']) { ?>
                            <span><i class="fa fa-users"></i> <?php echo htmlspecialchars($row['recipeYields']);?> <?php _e('servings'); ?></span>
                            <?php } ?>
                            <?php if($row['totalCalories'] > 0) { ?>
                            <span class="calorie-badge">🔥 <?php echo intval($row['totalCalories']);?> cal</span>
                            <?php } ?>
                        </div>
                        <?php if($avgRating > 0): ?>
                        <div class="recipe-stars-row">
                            <?php for($s=1;$s<=5;$s++): ?>
                            <i class="fa <?php echo $s <= round($avgRating) ? 'fa-star' : 'fa-star-o'; ?> star-icon"></i>
                            <?php endfor; ?>
                            <span class="star-score"><?php echo number_format($avgRating,1); ?></span>
                            <span class="star-count">(<?php echo $ratingCount; ?>)</span>
                        </div>
                        <?php endif; ?>
                        <?php if($row['FullName']) { ?>
                        <div class="recipe-author"><?php _e('by'); ?> <strong><?php echo htmlspecialchars($row['FullName']);?></strong></div>
                        <?php } ?>
                        <button class="fav-btn<?php echo $isFav ? ' favorited' : ''; ?>"
                                data-recipe-id="<?php echo intval($row['id']); ?>"
                                data-logged-in="<?php echo $isLoggedIn ? '1' : '0'; ?>"
                                title="<?php echo $isFav ? htmlspecialchars(__('Remove from favorites')) : htmlspecialchars(__('Save to favorites')); ?>">
                            <span class="fav-count"><?php echo $favCount; ?></span>
                            <i class="fa <?php echo $isFav ? 'fa-heart' : 'fa-heart-o'; ?> fav-icon"></i>
                        </button>
                    </div>
                </div>
<?php } ?>
            </div>

            <?php if($totalPages > 1): ?>
            <nav class="pagination-nav">
                <?php if($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>" class="page-btn">&laquo;</a>
                <?php endif; ?>
                <?php for($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
                <a href="?page=<?php echo $p; ?>" class="page-btn<?php echo $p==$page?' active':''; ?>"><?php echo $p; ?></a>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page+1; ?>" class="page-btn">&raquo;</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            </div>

        </div>
    </section>

<?php include_once('includes/footer.php');?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window._favI18n = {
        save:   '<?php echo addslashes(__('Save to favorites')); ?>',
        saved:  '<?php echo addslashes(__('Saved!')); ?>',
        remove: '<?php echo addslashes(__('Remove from favorites')); ?>'
    };
    </script>
    <script src="js/app.js"></script>
    <script>
    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('animate-in'); observer.unobserve(e.target); }});
        }, { threshold: 0.1 });
        document.querySelectorAll('.recipe-card').forEach(function(el) { observer.observe(el); });

        // ── INGREDIENT SEARCH DROPDOWN ──────────────
        var searchInput = document.getElementById('ingredientSearchInput');
        var listEl = document.getElementById('ingredientList');
        var tagsEl = document.getElementById('selectedTags');
        var searchBtn = document.getElementById('searchByIngredients');
        var resultsContainer = document.getElementById('ingredientSearchResults');
        var resultsGrid = document.getElementById('ingredientResultsGrid');
        var resultsCount = document.getElementById('ingredientResultsCount');
        var defaultList = document.getElementById('defaultRecipesList');
        var clearBtn = document.getElementById('clearIngredientSearch');
        var selectedIngredients = {}; // {id: name}
        var debounceTimer = null;

        // Load ingredients on focus
        searchInput.addEventListener('focus', function() {
            if (listEl.innerHTML.trim() === '<div class="no-results">Type to search...</div>' || listEl.innerHTML.trim() === '') {
                loadIngredients('');
            }
            listEl.classList.add('open');
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.multi-select-dropdown')) {
                listEl.classList.remove('open');
            }
        });

        // Search filter
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            var q = this.value.trim();
            debounceTimer = setTimeout(function() {
                loadIngredients(q);
            }, 300);
        });

        function loadIngredients(query) {
            fetch('api/search-ingredients.php?q=' + encodeURIComponent(query))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                listEl.innerHTML = '';
                if (data.length === 0) {
                    listEl.innerHTML = '<div class="no-results">No ingredients found</div>';
                } else {
                    data.forEach(function(ing) {
                        var label = document.createElement('label');
                        var cb = document.createElement('input');
                        cb.type = 'checkbox';
                        cb.value = ing.id;
                        cb.dataset.name = ing.name;
                        if (selectedIngredients[ing.id]) {
                            cb.checked = true;
                        }
                        cb.addEventListener('change', function() {
                            if (this.checked) {
                                selectedIngredients[ing.id] = ing.name;
                            } else {
                                delete selectedIngredients[ing.id];
                            }
                            renderTags();
                        });
                        var span = document.createElement('span');
                        span.textContent = ing.name;
                        label.appendChild(cb);
                        label.appendChild(span);
                        listEl.appendChild(label);
                    });
                }
                listEl.classList.add('open');
            })
            .catch(function() {
                listEl.innerHTML = '<div class="no-results">Error loading ingredients</div>';
            });
        }

        function renderTags() {
            tagsEl.innerHTML = '';
            var ids = Object.keys(selectedIngredients);
            ids.forEach(function(id) {
                var tag = document.createElement('span');
                tag.className = 'selected-tag';
                var nameNode = document.createTextNode(selectedIngredients[id] + ' ');
                var removeSpan = document.createElement('span');
                removeSpan.className = 'remove-tag';
                removeSpan.dataset.id = id;
                removeSpan.innerHTML = '&times;';
                tag.appendChild(nameNode);
                tag.appendChild(removeSpan);
                tagsEl.appendChild(tag);
            });
            searchBtn.disabled = ids.length === 0;

            // Update checkboxes
            listEl.querySelectorAll('input[type=checkbox]').forEach(function(cb) {
                cb.checked = !!selectedIngredients[cb.value];
            });
        }

        // Remove tag
        tagsEl.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-tag')) {
                delete selectedIngredients[e.target.dataset.id];
                renderTags();
            }
        });

        // Search recipes
        searchBtn.addEventListener('click', function() {
            var ids = Object.keys(selectedIngredients);
            if (ids.length === 0) return;

            searchBtn.disabled = true;
            searchBtn.textContent = '⏳ Searching...';

            fetch('api/search-by-ingredients.php?ids=' + ids.join(','))
            .then(function(r) { return r.json(); })
            .then(function(recipes) {
                resultsGrid.innerHTML = '';
                
                if (recipes.length === 0) {
                    resultsCount.textContent = 'No recipes found with all selected ingredients';
                    resultsGrid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;text-align:center;padding:40px;"><div style="font-size:48px;">🍽️</div><h3>No matches</h3><p>Try removing some ingredients</p></div>';
                } else {
                    resultsCount.textContent = recipes.length + ' recipe' + (recipes.length > 1 ? 's' : '') + ' found';
                    recipes.forEach(function(r) {
                        var rid = parseInt(r.id, 10) || 0;
                        var cardHtml = '<div class="recipe-card animate-in">' +
                            '<div class="recipe-card-image">' +
                            '<img src="user/images/' + escHtml(r.picture) + '" alt="' + escHtml(r.title) + '" loading="lazy">' +
                            '<div class="recipe-card-overlay"><a href="recipe-details.php?rid=' + rid + '" class="view-recipe-btn">View Recipe</a></div>' +
                            '</div>' +
                            '<div class="recipe-card-body">' +
                            '<h5><a href="recipe-details.php?rid=' + rid + '">' + escHtml(r.title) + '</a></h5>' +
                            '<div class="recipe-meta">' +
                            (r.prepTime ? '<span>⏱️ ' + escHtml(String(r.prepTime)) + ' min</span>' : '') +
                            (r.totalCalories > 0 ? '<span class="calorie-badge">🔥 ' + parseInt(r.totalCalories, 10) + ' cal</span>' : '') +
                            '</div></div></div>';
                        resultsGrid.insertAdjacentHTML('beforeend', cardHtml);
                    });
                }

                defaultList.style.display = 'none';
                resultsContainer.style.display = 'block';
                searchBtn.disabled = false;
                searchBtn.textContent = '🔍 Find Recipes';
            })
            .catch(function() {
                searchBtn.disabled = false;
                searchBtn.textContent = '🔍 Find Recipes';
                alert('Error searching recipes. Please try again.');
            });
        });

        // Clear filter
        clearBtn.addEventListener('click', function() {
            selectedIngredients = {};
            renderTags();
            resultsContainer.style.display = 'none';
            defaultList.style.display = 'block';
            searchInput.value = '';
        });
    });
    </script>
</body>
</html>