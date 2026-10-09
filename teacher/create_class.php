<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = sanitize($_POST['class_name']);
    $subject = sanitize($_POST['subject']);
    $teacher_id = $_SESSION['user_id'];
    
    // Generate random class code (unique 6 characters)
    $class_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    
    // Ensure uniqueness (simple check, for production, should be more robust)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE class_code = ?");
    $stmt->execute([$class_code]);
    if ($stmt->fetchColumn() > 0) {
        $class_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    }

    if (empty($class_name) || empty($subject)) {
        set_flash_message("Please fill in all fields.", "danger");
    } else {
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, subject, class_code, teacher_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$class_name, $subject, $class_code, $teacher_id]);
        
        set_flash_message("Class created successfully! Class Code: <strong>$class_code</strong>");
        redirect('dashboard.php');
    }
}

$page_title = "Create Class";
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4">Create New Class</h2>
                <form action="create_class.php" method="POST">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Class Name</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" placeholder="e.g. Physics Section A" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="e.g. Physics" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="dashboard.php" class="btn btn-light px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
