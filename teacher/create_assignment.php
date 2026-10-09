<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (!isset($_GET['class_id'])) {
    redirect('dashboard.php');
}

$class_id = $_GET['class_id'];
$teacher_id = $_SESSION['user_id'];

// Get class info
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$class_id, $teacher_id]);
$class = $stmt->fetch();

if (!$class) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $due_date = sanitize($_POST['due_date']);
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/assignments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['assignment_file']['name'];
        $file_path = 'uploads/assignments/' . $file_name;
        
        if (!move_uploaded_file($_FILES['assignment_file']['tmp_name'], '../' . $file_path)) {
            $file_path = null;
        }
    }

    if (empty($title) || empty($due_date)) {
        set_flash_message("Title and Due Date are required.", "danger");
    } else {
        $stmt = $pdo->prepare("INSERT INTO assignments (class_id, title, description, file_path, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$class_id, $title, $description, $file_path, $due_date]);
        
        set_flash_message("Assignment created successfully!");
        redirect("view_class.php?id=$class_id");
    }
}

$page_title = "Create Assignment";
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4">Create New Assignment</h2>
                <form action="create_assignment.php?class_id=<?php echo $class_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Assignment Title</label>
                        <input type="text" class="form-control" id="title" name="title" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Instructions)</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date & Time</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                    </div>
                    <div class="mb-4">
                        <label for="assignment_file" class="form-label">Attach File (Optional)</label>
                        <input type="file" class="form-control" id="assignment_file" name="assignment_file">
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="view_class.php?id=<?php echo $class_id; ?>" class="btn btn-light px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Create Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
