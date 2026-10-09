<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect($_SESSION['role'] . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            set_flash_message("Welcome back, " . $user['name'] . "!");
            redirect($user['role'] . '/dashboard.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$page_title = "Login";
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
            <img src="<?php echo $base_url; ?>/assets/img/fss-logo.png" alt="FSS Logo" height="80" class="mb-3 rounded-circle">
            <h2 class="mt-2">Sign in</h2>
            <p class="text-muted">FSS Online Classroom</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2">Sign in</button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0 text-muted">Don't have an account? <a href="register.php" class="text-decoration-none">Create account</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
