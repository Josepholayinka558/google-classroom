<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$user = get_user_data($pdo, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        set_flash_message("Name and Email are required.", "danger");
    } else {
        // Update name and email
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        // Update password if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
        }

        set_flash_message("Profile updated successfully!");
        redirect('profile.php');
    }
}

$page_title = "My Profile";
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                    <h2 class="mt-2">My Profile</h2>
                    <p class="text-muted">Manage your account information</p>
                </div>

                <form action="profile.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control bg-light" value="<?php echo ucfirst($user['role']); ?>" readonly>
                        <small class="text-muted">Role cannot be changed after registration.</small>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2">Update Profile</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="<?php echo $_SESSION['role']; ?>/dashboard.php" class="text-decoration-none">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
