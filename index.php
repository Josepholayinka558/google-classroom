<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

$page_title = "Welcome";
include 'includes/header.php';
?>

<div class="row align-items-center mt-5">
    <div class="col-md-6">
        <h1 class="display-4 fw-bold text-primary mb-4">Federal School of Surveying Online Classroom</h1>
        <p class="lead mb-4">Welcome to the official online classroom for FSS. Create classes, share assignments, and collaborate with ease.</p>
        
        <?php if (!is_logged_in()): ?>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="register.php" class="btn btn-primary btn-lg px-4 me-md-2">Get Started</a>
                <a href="login.php" class="btn btn-outline-secondary btn-lg px-4">Login</a>
            </div>
        <?php else: ?>
            <a href="<?php echo $_SESSION['role']; ?>/dashboard.php" class="btn btn-primary btn-lg px-4">Go to Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="col-md-6 text-center">
        <img src="<?php echo $base_url; ?>/assets/img/fss.jpg" alt="FSS Hero" class="img-fluid rounded shadow" style="max-height: 400px;">
    </div>
</div>

<div class="row mt-5 pt-5 text-center">
    <div class="col-md-4">
        <div class="p-3">
            <i class="bi bi-people fs-1 text-primary"></i>
            <h3 class="mt-3">Role Based</h3>
            <p>Separate dashboards for Admins, Teachers, and Students with specific features.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3">
            <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
            <h3 class="mt-3">Assignments</h3>
            <p>Easily create and submit assignments with file upload support.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3">
            <i class="bi bi-megaphone fs-1 text-primary"></i>
            <h3 class="mt-3">Announcements</h3>
            <p>Keep everyone updated with real-time class announcements.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
