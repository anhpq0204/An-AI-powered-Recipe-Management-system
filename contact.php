<?php include('includes/dbconnection.php');
require_once('includes/lang.php');

if(isset($_POST['submit'])) {
    $fname=$_POST['fname'];
    $emailid=$_POST['emailid'];
    $subject=$_POST['subject'];
    $message=$_POST['message'];
    $query=mysqli_query($con, "insert into enquiries(userName,userEmail,subject,commentMessage) value('$fname','$emailid','$subject','$message' )");
    if ($query) {
        echo "<script>alert('" . addslashes(__('Enquiry sent successfully. We will contact you shortly')) . "');</script>";
        echo "<script>window.location.href ='contact.php'</script>";
    } else {
        echo "<script>alert('" . addslashes(__('Something went wrong. Please try again.')) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Contact Food Recipe System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Food Recipe System | Contact</title>
    <link rel="icon" href="img/core-img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include_once('includes/topbar.php');?>
<?php include_once('includes/header.php');?>

    <!-- Page Header -->
    <section class="page-header-section" style="background-image: url(img/bg-img/breadcumb4.jpg);">
        <div class="page-header-overlay"></div>
        <div class="container">
            <div class="page-header-content">
                <span class="page-tag"><?php _e('Contact'); ?></span>
                <h1><?php _e('Get In Touch'); ?></h1>
                <p><?php _e('We\'d love to hear from you'); ?></p>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-section" style="padding: 100px 0;">
        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-12 col-lg-5 mb-5 mb-lg-0">
<?php
$ret = mysqli_query($con, "SELECT * FROM pages WHERE PageType='contactus'");
while ($row = mysqli_fetch_array($ret)) {
?>
                    <div class="contact-info-card">
                        <div class="modern-section-heading" style="text-align: left; margin-bottom: 30px;">
                            <span class="section-tag" style="margin-bottom: 15px;"><?php _e('Contact Info'); ?></span>
                            <h2 style="font-size: 32px;"><?php echo htmlspecialchars($row['PageTitle']);?></h2>
                        </div>
                        <div class="contact-text">
                            <?php echo htmlspecialchars($row['PageDescription']);?>
                        </div>
                    </div>
<?php } ?>
                </div>

                <!-- Contact Form -->
                <div class="col-12 col-lg-7">
                    <div class="recipe-detail-card" style="margin-bottom: 0;">
                        <h3 style="margin-bottom: 25px;"><?php _e('Send a Message'); ?></h3>
                        <form method="post" class="modern-form">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group-modern">
                                        <input type="text" name="fname" placeholder="<?php echo htmlspecialchars(__('Your Name')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group-modern">
                                        <input type="email" name="emailid" placeholder="<?php echo htmlspecialchars(__('Your Email')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <input type="text" name="subject" placeholder="<?php echo htmlspecialchars(__('Subject')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <textarea name="message" rows="5" placeholder="<?php echo htmlspecialchars(__('Message')); ?>" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="submit" class="btn-modern btn-primary-modern"><?php _e('Send Message'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="modern-gallery-section">
        <div class="container">
            <div class="modern-section-heading">
                <span class="section-tag"><?php _e('Gallery'); ?></span>
                <h2><?php echo __('Food <span class="highlight">Inspiration</span>'); ?></h2>
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
