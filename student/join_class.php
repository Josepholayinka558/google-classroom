<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code = sanitize($_POST['class_code']);
    $student_id = $_SESSION['user_id'];

    if (empty($class_code)) {
        set_flash_message("Please enter a class code.", "danger");
    } else {
        // Find class by code
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE class_code = ?");
        $stmt->execute([$class_code]);
        $class = $stmt->fetch();

        if ($class) {
            $class_id = $class['id'];
            // Check if already enrolled
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ? AND class_id = ?");
            $stmt->execute([$student_id, $class_id]);
            if ($stmt->fetchColumn() > 0) {
                set_flash_message("You are already enrolled in this class.", "warning");
            } else {
                // Enroll student
                $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $class_id]);
                set_flash_message("Successfully joined the class!");
                redirect('dashboard.php');
            }
        } else {
            set_flash_message("Invalid class code. Please try again.", "danger");
        }
    }
}

$page_title = "Join Class";
include '../includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-plus-circle-fill text-primary fs-1"></i>
                    <h2 class="mt-2">Join Class</h2>
                    <p class="text-muted">Ask your teacher for the class code, then enter it here.</p>
                </div>

                <form action="join_class.php" method="POST">
                    <div class="mb-4">
                        <label for="class_code" class="form-label">Class Code</label>
                        <input type="text" class="form-control form-control-lg text-center fw-bold" id="class_code" name="class_code" maxlength="10" placeholder="e.g. 7g2f3h" required autofocus>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Join Class</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="dashboard.php" class="text-decoration-none">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
