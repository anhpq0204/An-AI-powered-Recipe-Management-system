<?php include('includes/dbconnection.php');?>
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
                <h1>All Recipes</h1>
                <p>Discover our complete collection of delicious recipes</p>
            </div>
        </div>
    </section>

    <!-- Recipes Grid -->
    <section class="featured-recipes-section">
        <div class="container">

            <!-- Ingredient Search Filter -->
            <div class="ingredient-filter-card" id="ingredientFilter">
                <h4>🔍 Filter by Ingredients</h4>
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
            <div class="recipes-grid">
<?php
$ret = mysqli_query($con, "SELECT r.recipeTitle, r.recipePicture, r.id, r.postingDate, r.recipePrepTime, r.recipeYields, r.totalCalories, u.FullName 
    FROM recipes r 
    LEFT JOIN users u ON r.userId = u.ID 
    ORDER BY r.id DESC");
while ($row = mysqli_fetch_array($ret)) {
?>
                <div class="recipe-card">
                    <div class="recipe-card-image">
                        <img src="user/images/<?php echo htmlspecialchars($row['recipePicture']);?>" alt="<?php echo htmlspecialchars($row['recipeTitle']);?>" loading="lazy">
                        <div class="recipe-card-overlay">
                            <a href="recipe-details.php?rid=<?php echo intval($row['id']);?>" class="view-recipe-btn">View Recipe</a>
                        </div>
                    </div>
                    <div class="recipe-card-body">
                        <h5><a href="recipe-details.php?rid=<?php echo intval($row['id']);?>"><?php echo htmlspecialchars($row['recipeTitle']);?></a></h5>
                        <div class="recipe-meta">
                            <?php if($row['recipePrepTime']) { ?>
                            <span><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($row['recipePrepTime']);?> min</span>
                            <?php } ?>
                            <?php if($row['recipeYields']) { ?>
                            <span><i class="fa fa-users"></i> <?php echo htmlspecialchars($row['recipeYields']);?> servings</span>
                            <?php } ?>
                            <?php if($row['totalCalories'] > 0) { ?>
                            <span class="calorie-badge">🔥 <?php echo intval($row['totalCalories']);?> cal</span>
                            <?php } ?>
                        </div>
                        <?php if($row['FullName']) { ?>
                        <div class="recipe-author">by <strong><?php echo htmlspecialchars($row['FullName']);?></strong></div>
                        <?php } ?>
                    </div>
                </div>
<?php } ?>
            </div>
            </div>

        </div>
    </section>

<?php include_once('includes/footer.php');?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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