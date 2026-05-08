<?php include('includes/dbconnection.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Food Recipe System - Discover and share delicious recipes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Food Recipe System | Home</title>

    <link rel="icon" href="img/core-img/favicon.ico">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Preloader -->
<?php include_once('includes/topbar.php');?>

    <!-- ##### Header Area Start ##### -->
   <?php include_once('includes/header.php');?>
    <!-- ##### Header Area End ##### -->

    <!-- ##### Hero Section ##### -->
    <section class="modern-hero">
        <div class="hero-slider" id="heroSlider">
            <div class="hero-slide active" style="background-image: url(img/bg-img/bg1.jpg);"></div>
            <div class="hero-slide" style="background-image: url(img/bg-img/bg6.jpg);"></div>
            <div class="hero-slide" style="background-image: url(img/bg-img/bg7.jpg);"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="hero-badge">🍳 Welcome to FRS</span>
            <h1>Discover <span class="highlight">Delicious</span> Recipes</h1>
            <p>Explore hundreds of recipes from talented home cooks. Share your favorites and inspire others.</p>
            <div class="hero-buttons">
                <a href="recipes.php" class="btn-modern btn-primary-modern">Explore Recipes</a>
                <a href="user/login.php" class="btn-modern btn-outline-modern">Share Yours</a>
            </div>
        </div>
        <!-- Slider dots -->
        <div class="slider-dots">
            <span class="dot active" data-slide="0"></span>
            <span class="dot" data-slide="1"></span>
            <span class="dot" data-slide="2"></span>
        </div>
    </section>

    <!-- ##### Stats Section ##### -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-row">
                <?php
                $totalRecipes = mysqli_num_rows(mysqli_query($con, "SELECT id FROM recipes"));
                $totalUsers = mysqli_num_rows(mysqli_query($con, "SELECT id FROM users"));
                $totalComments = mysqli_num_rows(mysqli_query($con, "SELECT id FROM comments"));
                ?>
                <div class="stat-item">
                    <div class="stat-icon">🍽️</div>
                    <div class="stat-number" data-count="<?php echo $totalRecipes;?>"><?php echo $totalRecipes;?></div>
                    <div class="stat-label">Recipes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">👨‍🍳</div>
                    <div class="stat-number" data-count="<?php echo $totalUsers;?>"><?php echo $totalUsers;?></div>
                    <div class="stat-label">Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">💬</div>
                    <div class="stat-number" data-count="<?php echo $totalComments;?>"><?php echo $totalComments;?></div>
                    <div class="stat-label">Comments</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ##### Featured Recipes Section ##### -->
    <section class="featured-recipes-section">
        <div class="container">
            <div class="modern-section-heading">
                <span class="section-tag">Latest Recipes</span>
                <h2>Recently Added <span class="highlight">Recipes</span></h2>
                <p>Fresh dishes from our community of home cooks</p>
            </div>

            <div class="recipes-grid">
<?php
$ret = mysqli_query($con, "SELECT r.recipeTitle, r.recipePicture, r.id, r.recipePrepTime, r.recipeCookTime, r.recipeYields, r.totalCalories, u.FullName 
    FROM recipes r 
    LEFT JOIN users u ON r.userId = u.ID 
    ORDER BY r.id DESC LIMIT 6");
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
                        <div class="recipe-author">
                            <span>by <strong><?php echo htmlspecialchars($row['FullName']);?></strong></span>
                        </div>
                        <?php } ?>
                    </div>
                </div>
<?php } ?>
            </div>

            <div class="text-center" style="margin-top: 40px;">
                <a href="recipes.php" class="btn-modern btn-primary-modern">View All Recipes →</a>
            </div>
        </div>
    </section>

    <!-- ##### Gallery Section ##### -->
    <section class="modern-gallery-section">
        <div class="container">
            <div class="modern-section-heading">
                <span class="section-tag">Gallery</span>
                <h2>Food <span class="highlight">Inspiration</span></h2>
            </div>
        </div>
        <div class="gallery-grid">
            <div class="gallery-item"><img src="img/bg-img/insta1.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta2.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta3.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta4.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta5.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta6.jpg" alt="Food gallery" loading="lazy"></div>
            <div class="gallery-item"><img src="img/bg-img/insta7.jpg" alt="Food gallery" loading="lazy"></div>
        </div>
    </section>

    <!-- ##### CTA Section ##### -->
    <section class="modern-cta-section" style="background-image: url(img/bg-img/bg5.jpg);">
        <div class="cta-overlay"></div>
        <div class="container">
            <div class="cta-content">
                <h2>Ready to share your recipe?</h2>
                <p>Join our community and share your culinary creations with the world</p>
                <a href="user/signup.php" class="btn-modern btn-light-modern">Get Started Free</a>
            </div>
        </div>
    </section>

    <!-- ##### Footer ##### -->
<?php include_once('includes/footer.php');?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App JS -->
    <script src="js/app.js"></script>
    
    <!-- Homepage specific JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero Slider
        var slides = document.querySelectorAll('.hero-slide');
        var dots = document.querySelectorAll('.slider-dots .dot');
        var current = 0;

        function showSlide(index) {
            slides.forEach(function(s, i) {
                s.classList.toggle('active', i === index);
            });
            dots.forEach(function(d, i) {
                d.classList.toggle('active', i === index);
            });
            current = index;
        }

        if (slides.length > 1) {
            setInterval(function() {
                showSlide((current + 1) % slides.length);
            }, 5000);
        }

        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                showSlide(parseInt(this.getAttribute('data-slide')));
            });
        });

        // Animate stats on scroll
        var statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.stat-item, .recipe-card, .gallery-item').forEach(function(el) {
            statsObserver.observe(el);
        });
    });
    </script>
</body>

</html>