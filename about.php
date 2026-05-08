<?php include('includes/dbconnection.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="About Food Recipe System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | About</title>
    <link rel="icon" href="img/core-img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once('includes/topbar.php');?>
<?php include_once('includes/header.php');?>

    <!-- Page Header -->
    <section class="page-header-section" style="background-image: url(img/bg-img/breadcumb1.jpg);">
        <div class="page-header-overlay"></div>
        <div class="container">
            <div class="page-header-content">
                <span class="page-tag">About</span>
                <h1>About Us</h1>
                <p>Learn more about Food Recipe System</p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content-section">
        <div class="container">
<?php
$ret = mysqli_query($con, "SELECT * FROM pages WHERE PageType='aboutus'");
while ($row = mysqli_fetch_array($ret)) {
?>
            <div class="about-card">
                <div class="modern-section-heading">
                    <h2><?php echo htmlspecialchars($row['PageTitle']);?></h2>
                </div>
                <div class="about-text">
                    <?php echo htmlspecialchars($row['PageDescription']);?>
                </div>
            </div>
<?php } ?>
        </div>
    </section>

    <!-- Gallery -->
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

<?php include_once('includes/footer.php');?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('animate-in'); observer.unobserve(e.target); }});
        }, { threshold: 0.1 });
        document.querySelectorAll('.gallery-item').forEach(function(el) { observer.observe(el); });
    });
    </script>
</body>
</html>