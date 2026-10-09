<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$class_id = $_GET['id'];
$teacher_id = $_SESSION['user_id'];

// Get class info and verify ownership
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$class_id, $teacher_id]);
$class = $stmt->fetch();

if (!$class) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = sanitize($_POST['class_name']);
    $subject = sanitize($_POST['subject']);

    if (empty($class_name) || empty($subject)) {
        set_flash_message("Please fill in all fields.", "danger");
    } else {
        $stmt = $pdo->prepare("UPDATE classes SET class_name = ?, subject = ? WHERE id = ?");
        $stmt->execute([$class_name, $subject, $class_id]);
        
        set_flash_message("Class updated successfully!");
        redirect('dashboard.php');
    }
}

$page_title = "Edit Class";
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4">Edit Class</h2>
                <form action="edit_class.php?id=<?php echo $class_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Class Name</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo $class['class_name']; ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $class['subject']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Code</label>
                        <input type="text" class="form-control bg-light" value="<?php echo $class['class_code']; ?>" readonly>
                        <small class="text-muted">Class code cannot be changed.</small>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="dashboard.php" class="btn btn-light px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
