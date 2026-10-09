<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect($_SESSION['role'] . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email address already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $role]);

            set_flash_message("Registration successful. Please login.");
            redirect('login.php');
        }
    }
}

$page_title = "Create Account";
include 'includes/header.php';
?>

<div class="card login-register-card shadow-sm mx-auto">
    <div class="card-body p-4">
        <div class="mb-3">
            <a href="index.php" class="text-decoration-none small d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Back to Home
            </a>
        </div>
        <div class="text-center mb-4">
            <img src="<?php echo $base_url; ?>/assets/img/fss-logo.png" alt="FSS Logo" height="80" class="mb-3 rounded-circle border">
            <h2 class="mt-2">Create Account</h2>
            <p class="text-muted">Federal School of Surveying</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required autofocus>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Register As</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" selected disabled>Choose your role</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2">Create Account</button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0 text-muted">Already have an account? <a href="login.php" class="text-decoration-none">Sign in</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
