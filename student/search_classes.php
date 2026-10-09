<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

$student_id = $_SESSION['user_id'];
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';

// Get classes matching search that student is NOT enrolled in
if ($search) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.name as teacher_name 
        FROM classes c 
        JOIN users u ON c.teacher_id = u.id 
        WHERE (c.class_name LIKE ? OR c.subject LIKE ?) 
        AND c.id NOT IN (SELECT class_id FROM enrollments WHERE student_id = ?)
    ");
    $stmt->execute(["%$search%", "%$search%", $student_id]);
    $available_classes = $stmt->fetchAll();
} else {
    $available_classes = [];
}

$page_title = "Search Classes";
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Search New Classes</h2>
        
        <form action="search_classes.php" method="GET" class="mb-5">
            <div class="input-group input-group-lg shadow-sm">
                <input type="text" name="q" class="form-control" placeholder="Search by class name or subject..." value="<?php echo $search; ?>" required>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>

        <?php if ($search): ?>
            <h5 class="mb-3">Results for "<?php echo $search; ?>"</h5>
            <?php if (empty($available_classes)): ?>
                <div class="alert alert-info">No new classes found matching your search.</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($available_classes as $class): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $class['class_name']; ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $class['subject']; ?></h6>
                                    <p class="card-text small">Teacher: <?php echo $class['teacher_name']; ?></p>
                                    <p class="card-text small">Class Code: <strong><?php echo $class['class_code']; ?></strong></p>
                                    <form action="join_class.php" method="POST">
                                        <input type="hidden" name="class_code" value="<?php echo $class['class_code']; ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">Join Class</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
