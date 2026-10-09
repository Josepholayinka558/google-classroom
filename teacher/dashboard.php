<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

$teacher_id = $_SESSION['user_id'];

// Get all classes taught by this teacher
$stmt = $pdo->prepare("SELECT * FROM classes WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->execute([$teacher_id]);
$classes = $stmt->fetchAll();

$page_title = "Teacher Dashboard";
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Classes</h2>
    <a href="create_class.php" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-plus-lg me-1"></i> Create Class
    </a>
</div>

<?php if (empty($classes)): ?>
    <div class="text-center py-5">
        <img src="https://www.gstatic.com/classroom/empty_states_v2/home-page-hero.svg" alt="Empty Classes" class="img-fluid mb-4" style="max-height: 200px;">
        <h3>No classes yet</h3>
        <p class="text-muted">Create your first class to get started!</p>
        <a href="create_class.php" class="btn btn-primary px-4 mt-2">Create Class</a>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($classes as $class): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="class-card-header" style="background-color: #1a73e8; background-image: url('https://www.gstatic.com/classroom/themes/Honors.jpg');">
                        <h5><a href="view_class.php?id=<?php echo $class['id']; ?>" class="text-white text-decoration-none"><?php echo $class['class_name']; ?></a></h5>
                        <p class="small"><?php echo $class['subject']; ?></p>
                        <p class="small mt-2">Code: <strong><?php echo $class['class_code']; ?></strong></p>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mt-auto d-flex justify-content-end">
                            <a href="view_class.php?id=<?php echo $class['id']; ?>" class="btn btn-outline-primary btn-sm me-2">View Class</a>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="edit_class.php?id=<?php echo $class['id']; ?>">Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="delete_class.php?id=<?php echo $class['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
