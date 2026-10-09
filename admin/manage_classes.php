<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('admin');

// Handle class deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$delete_id]);
    set_flash_message("Class deleted successfully!");
    redirect('manage_classes.php');
}

// Get all classes
$stmt = $pdo->prepare("SELECT c.*, u.name as teacher_name FROM classes c JOIN users u ON c.teacher_id = u.id ORDER BY c.created_at DESC");
$stmt->execute();
$classes = $stmt->fetchAll();

$page_title = "Manage Classes";
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Classes</h2>
    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Class Name</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Code</th>
                        <th>Created At</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No classes created in the system.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-primary"><?php echo $class['class_name']; ?></td>
                            <td><?php echo $class['subject']; ?></td>
                            <td><?php echo $class['teacher_name']; ?></td>
                            <td><code class="text-primary"><?php echo $class['class_code']; ?></code></td>
                            <td class="small text-muted"><?php echo date("F j, Y", strtotime($class['created_at'])); ?></td>
                            <td class="pe-4 text-end">
                                <a href="manage_classes.php?delete_id=<?php echo $class['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this class and all its data?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
