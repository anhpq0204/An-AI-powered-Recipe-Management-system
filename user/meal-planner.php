<?php
require_once('../includes/session.php');
include('../includes/dbconnection.php');
if (!isset($_SESSION['frsuid']) || strlen($_SESSION['frsuid']) == 0) {
    header('location:logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Meal Planner</title>
    <style>
        .recipe-search-result {
            display:flex; align-items:center; gap:12px;
            padding:10px 12px; border-radius:10px; border:1px solid #eee;
            margin-bottom:8px; transition:background .15s;
        }
        .recipe-search-result:hover { background:#f8f9fa; }
        .recipe-search-thumb { width:52px; height:52px; object-fit:cover; border-radius:8px; flex-shrink:0; }
        .recipe-search-info { flex:1; min-width:0; }
        .recipe-search-info .title { font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .recipe-search-info .meta { font-size:.8rem; color:#888; }
        .selected-recipe-card {
            display:flex; align-items:center; gap:10px;
            padding:8px 12px; border-radius:8px; background:#f0f7ff;
            border:1px solid #c8e0ff; margin-bottom:6px;
        }
        .selected-recipe-card .title { flex:1; font-size:.9rem; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .selected-recipe-thumb { width:40px; height:40px; object-fit:cover; border-radius:6px; flex-shrink:0; }
        .shopping-list-item { border-bottom:1px solid #f0f0f0; }
        .shopping-list-item:last-child { border-bottom:none; }
        #searchResultsBox { max-height:380px; overflow-y:auto; }
        .spinner-text { color:#888; font-size:.9rem; padding:12px 0; }
        #ingredientPanel { min-height:200px; }
        .save-alert { display:none; margin-top:10px; }
    </style>
</head>
<body>
<section id="container">

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<section id="main-content">
    <section class="wrapper">

        <div class="d-flex align-items-center justify-content-between mb-1">
            <h1 class="user-page-title mb-0">
                Meal Planner
                <small>Select recipes and get your shopping list</small>
            </h1>
            <a href="my-meal-plans.php" class="btn btn-sm btn-outline-secondary">📋 My Plans</a>
        </div>

        <div class="row g-4 mt-1">

            <!-- LEFT: Recipe Picker -->
            <div class="col-12 col-lg-5">
                <div class="user-content-card">
                    <h5 class="mb-3">🔍 Search Recipes</h5>
                    <input type="text" id="recipeSearchInput" class="form-control mb-3"
                           placeholder="Type recipe name..." autocomplete="off">
                    <div id="searchResultsBox">
                        <p class="spinner-text text-center">Start typing to search recipes…</p>
                    </div>
                </div>

                <!-- Current selection -->
                <div class="user-content-card mt-3" id="selectionPanel" style="display:none;">
                    <h5 class="mb-3">✅ Selected Recipes <span class="badge bg-primary" id="selectionCount">0</span></h5>
                    <div id="selectionList"></div>
                </div>
            </div>

            <!-- RIGHT: Shopping List + Save -->
            <div class="col-12 col-lg-7">
                <div class="user-content-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">🛒 Shopping List</h5>
                        <span id="calorieBadge" class="badge bg-warning text-dark" style="display:none;"></span>
                    </div>

                    <div id="ingredientPanel">
                        <p class="text-muted text-center py-4">Add recipes on the left to see your shopping list.</p>
                    </div>

                    <!-- Save plan form -->
                    <div id="savePlanSection" style="display:none; border-top:1px solid #eee; padding-top:16px; margin-top:16px;">
                        <div class="d-flex gap-2">
                            <input type="text" id="planNameInput" class="form-control"
                                   placeholder="Plan name (e.g. Week 1, Family Dinner)" maxlength="200">
                            <button id="savePlanBtn" class="btn btn-primary" style="white-space:nowrap;">💾 Save Plan</button>
                        </div>
                        <div id="saveAlert" class="save-alert alert"></div>
                    </div>
                </div>
            </div>

        </div>

    </section>
    <?php include_once('includes/footer.php');?>
</section>

</section>
<script src="../dashboard-assets/js/bootstrap.bundle.min.js"></script>
<script src="../dashboard-assets/js/app.js"></script>
<script>
(function() {
    var searchInput   = document.getElementById('recipeSearchInput');
    var resultsBox    = document.getElementById('searchResultsBox');
    var selectionPanel= document.getElementById('selectionPanel');
    var selectionList = document.getElementById('selectionList');
    var selectionCount= document.getElementById('selectionCount');
    var ingredientPanel= document.getElementById('ingredientPanel');
    var calorieBadge  = document.getElementById('calorieBadge');
    var savePlanSection= document.getElementById('savePlanSection');
    var planNameInput = document.getElementById('planNameInput');
    var savePlanBtn   = document.getElementById('savePlanBtn');
    var saveAlert     = document.getElementById('saveAlert');

    // {id: {title, picture, prepTime, totalCalories}}
    var selected = {};
    var debounceTimer = null;

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    // ── Search ──────────────────────────────────────────────
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = this.value.trim();
        debounceTimer = setTimeout(function() { loadRecipes(q); }, 300);
    });

    searchInput.addEventListener('focus', function() {
        if (resultsBox.querySelector('.spinner-text')) {
            loadRecipes('');
        }
    });

    function loadRecipes(query) {
        resultsBox.innerHTML = '<p class="spinner-text text-center">⏳ Searching…</p>';
        fetch('../api/search-recipes.php?q=' + encodeURIComponent(query))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            resultsBox.innerHTML = '';
            if (data.length === 0) {
                resultsBox.innerHTML = '<p class="spinner-text text-center">No recipes found.</p>';
                return;
            }
            data.forEach(function(r) {
                var isAdded = !!selected[r.id];
                var card = document.createElement('div');
                card.className = 'recipe-search-result';
                card.innerHTML =
                    '<img class="recipe-search-thumb" src="../user/images/' + escHtml(r.picture) + '" alt="">' +
                    '<div class="recipe-search-info">' +
                    '<div class="title">' + escHtml(r.title) + '</div>' +
                    '<div class="meta">' +
                    (r.prepTime ? '⏱️ ' + r.prepTime + ' min ' : '') +
                    (r.totalCalories > 0 ? '🔥 ' + r.totalCalories + ' cal' : '') +
                    '</div></div>' +
                    '<button class="btn btn-sm ' + (isAdded ? 'btn-success disabled' : 'btn-outline-primary') + '" ' +
                    'data-id="' + r.id + '" data-title="' + escHtml(r.title) + '" ' +
                    'data-picture="' + escHtml(r.picture) + '" ' +
                    'data-preptime="' + r.prepTime + '" data-cal="' + r.totalCalories + '">' +
                    (isAdded ? '✓ Added' : '+ Add') + '</button>';
                resultsBox.appendChild(card);
            });
        })
        .catch(function() {
            resultsBox.innerHTML = '<p class="spinner-text text-center text-danger">Error loading recipes.</p>';
        });
    }

    // ── Add recipe via button click ──────────────────────────
    resultsBox.addEventListener('click', function(e) {
        var btn = e.target.closest('button[data-id]');
        if (!btn || btn.classList.contains('disabled')) return;
        var id = parseInt(btn.dataset.id, 10);
        selected[id] = {
            title:   btn.dataset.title,
            picture: btn.dataset.picture,
            prepTime:parseInt(btn.dataset.preptime, 10),
            calories:parseInt(btn.dataset.cal, 10),
        };
        btn.className = 'btn btn-sm btn-success disabled';
        btn.textContent = '✓ Added';
        renderSelection();
        fetchIngredients();
    });

    // ── Render selection list ────────────────────────────────
    function renderSelection() {
        var ids = Object.keys(selected);
        selectionCount.textContent = ids.length;
        selectionList.innerHTML = '';
        ids.forEach(function(id) {
            var r = selected[id];
            var card = document.createElement('div');
            card.className = 'selected-recipe-card';
            card.innerHTML =
                '<img class="selected-recipe-thumb" src="../user/images/' + escHtml(r.picture) + '" alt="">' +
                '<span class="title">' + escHtml(r.title) + '</span>' +
                '<button class="btn btn-sm btn-outline-danger" data-remove="' + id + '">✕</button>';
            selectionList.appendChild(card);
        });
        selectionPanel.style.display = ids.length > 0 ? 'block' : 'none';
        savePlanSection.style.display = ids.length > 0 ? 'block' : 'none';
    }

    // ── Remove recipe ────────────────────────────────────────
    selectionList.addEventListener('click', function(e) {
        var btn = e.target.closest('button[data-remove]');
        if (!btn) return;
        var id = parseInt(btn.dataset.remove, 10);
        delete selected[id];
        // re-render search results to un-mark
        var searchBtn = resultsBox.querySelector('button[data-id="' + id + '"]');
        if (searchBtn) {
            searchBtn.className = 'btn btn-sm btn-outline-primary';
            searchBtn.textContent = '+ Add';
        }
        renderSelection();
        fetchIngredients();
    });

    // ── Fetch aggregated ingredients ─────────────────────────
    function fetchIngredients() {
        var ids = Object.keys(selected);
        if (ids.length === 0) {
            ingredientPanel.innerHTML = '<p class="text-muted text-center py-4">Add recipes on the left to see your shopping list.</p>';
            calorieBadge.style.display = 'none';
            return;
        }
        ingredientPanel.innerHTML = '<p class="spinner-text text-center">⏳ Loading ingredients…</p>';
        fetch('../api/meal-plan-ingredients.php?recipe_ids=' + ids.join(','))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderIngredients(data);
        })
        .catch(function() {
            ingredientPanel.innerHTML = '<p class="text-danger text-center">Error loading ingredients.</p>';
        });
    }

    function renderIngredients(data) {
        if (!data.ingredients || data.ingredients.length === 0) {
            ingredientPanel.innerHTML = '<p class="text-muted text-center py-3">No ingredient data for selected recipes yet.</p>';
            calorieBadge.style.display = 'none';
            return;
        }
        if (data.total_calories > 0) {
            calorieBadge.textContent = '🔥 ' + data.total_calories + ' cal total';
            calorieBadge.style.display = '';
        } else {
            calorieBadge.style.display = 'none';
        }

        var html = '<p class="text-muted small mb-2">Check off as you gather each ingredient.</p><ul class="list-unstyled mb-0">';
        data.ingredients.forEach(function(ing) {
            var nameDisplay = ing.name_vi
                ? escHtml(ing.name_vi) + ' <span class="text-muted small">/ ' + escHtml(ing.name) + '</span>'
                : escHtml(ing.name);
            html += '<li class="shopping-list-item">' +
                '<label class="d-flex align-items-center gap-2 py-2" style="cursor:pointer;">' +
                '<input type="checkbox" class="shopping-check form-check-input mt-0" style="flex-shrink:0;">' +
                '<span class="shopping-label">' + nameDisplay + '</span>' +
                '<span class="ms-auto text-muted small">' + escHtml(ing.display) + '</span>' +
                '</label></li>';
        });
        html += '</ul>';
        ingredientPanel.innerHTML = html;

        ingredientPanel.querySelectorAll('.shopping-check').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var lbl = this.closest('label');
                if (this.checked) {
                    lbl.style.opacity = '0.45';
                    lbl.querySelector('.shopping-label').style.textDecoration = 'line-through';
                } else {
                    lbl.style.opacity = '';
                    lbl.querySelector('.shopping-label').style.textDecoration = '';
                }
            });
        });
    }

    // ── Save plan ────────────────────────────────────────────
    savePlanBtn.addEventListener('click', function() {
        var name = planNameInput.value.trim();
        if (!name) { planNameInput.focus(); return; }
        var ids = Object.keys(selected);
        if (ids.length === 0) return;

        savePlanBtn.disabled = true;
        savePlanBtn.textContent = '⏳ Saving…';
        saveAlert.style.display = 'none';

        var form = new FormData();
        form.append('plan_name', name);
        form.append('recipe_ids', ids.join(','));

        fetch('../api/save-meal-plan.php', { method: 'POST', body: form })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                saveAlert.className = 'save-alert alert alert-success';
                saveAlert.innerHTML = '✅ Plan saved! <a href="my-meal-plans.php">View in My Plans</a> or <a href="view-meal-plan.php?plan_id=' + data.plan_id + '">View plan</a>.';
                saveAlert.style.display = 'block';
                planNameInput.value = '';
            } else {
                saveAlert.className = 'save-alert alert alert-danger';
                saveAlert.textContent = '❌ ' + (data.error || 'Could not save plan.');
                saveAlert.style.display = 'block';
            }
        })
        .catch(function() {
            saveAlert.className = 'save-alert alert alert-danger';
            saveAlert.textContent = '❌ Error saving plan. Please try again.';
            saveAlert.style.display = 'block';
        })
        .finally(function() {
            savePlanBtn.disabled = false;
            savePlanBtn.textContent = '💾 Save Plan';
        });
    });

    // Load initial recipes on page load
    loadRecipes('');
})();
</script>
</body>
</html>
