<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (!isset($_GET['class_id'])) {
    redirect('dashboard.php');
}

$class_id = $_GET['class_id'];
$teacher_id = $_SESSION['user_id'];

// Get class info and verify ownership
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$class_id, $teacher_id]);
$class = $stmt->fetch();

if (!$class) {
    redirect('dashboard.php');
}

// Get enrolled students
$stmt = $pdo->prepare("SELECT u.* FROM users u JOIN enrollments e ON u.id = e.student_id WHERE e.class_id = ? ORDER BY u.name ASC");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll();

$page_title = "Students in " . $class['class_name'];
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Enrolled Students</h2>
    <a href="view_class.php?id=<?php echo $class_id; ?>" class="btn btn-outline-secondary btn-sm">Back to Class</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student Name</th>
                        <th>Email Address</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No students enrolled in this class yet.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="ps-4 d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-2 text-secondary"></i>
                                <?php echo $student['name']; ?>
                            </td>
                            <td><?php echo $student['email']; ?></td>
                            <td class="pe-4 text-end">
                                <a href="remove_student.php?student_id=<?php echo $student['id']; ?>&class_id=<?php echo $class_id; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove student from class?')">
                                    <i class="bi bi-person-dash"></i> Remove
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
