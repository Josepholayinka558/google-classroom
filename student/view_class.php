<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$class_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

// Get class info and verify enrollment
$stmt = $pdo->prepare("SELECT c.*, u.name as teacher_name FROM classes c JOIN enrollments e ON c.id = e.class_id JOIN users u ON c.teacher_id = u.id WHERE c.id = ? AND e.student_id = ?");
$stmt->execute([$class_id, $student_id]);
$class = $stmt->fetch();

if (!$class) {
    redirect('dashboard.php');
}

// Get announcements
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE class_id = ? ORDER BY created_at DESC");
$stmt->execute([$class_id]);
$announcements = $stmt->fetchAll();

// Get assignments
$stmt = $pdo->prepare("SELECT a.*, s.grade, s.submitted_at FROM assignments a LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = ? WHERE a.class_id = ? ORDER BY a.created_at DESC");
$stmt->execute([$student_id, $class_id]);
$assignments = $stmt->fetchAll();

$page_title = $class['class_name'];
include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="class-card-header p-4 rounded shadow-sm d-flex flex-column justify-content-end" style="background-color: #1a73e8; background-image: url('https://www.gstatic.com/classroom/themes/Honors.jpg'); height: 200px;">
            <h1 class="text-white"><?php echo $class['class_name']; ?></h1>
            <p class="text-white lead mb-0"><?php echo $class['subject']; ?></p>
            <p class="text-white small mb-0 mt-2">Teacher: <span class="fw-bold bg-white text-primary px-2 py-1 rounded"><?php echo $class['teacher_name']; ?></span></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Classroom Info</h5>
                <p class="small text-muted mb-0">Total Students: 
                    <?php 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE class_id = ?");
                    $stmt->execute([$class_id]);
                    echo $stmt->fetchColumn();
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Announcements & Assignments List -->
        <div class="mb-4">
            <ul class="nav nav-tabs mb-4" id="classTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream">Stream</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments">Classwork</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="stream">
                    <?php if (empty($announcements) && empty($assignments)): ?>
                        <div class="text-center py-5 bg-white border rounded">
                            <i class="bi bi-megaphone fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No announcements yet. Your teacher hasn't posted anything.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-box shadow-sm mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                <div>
                                    <h6 class="mb-0"><?php echo $class['teacher_name']; ?></h6>
                                    <small class="text-muted"><?php echo date("F j, Y", strtotime($announcement['created_at'])); ?></small>
                                </div>
                            </div>
                            <p class="mb-0"><?php echo nl2br($announcement['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tab-pane fade" id="assignments">
                    <?php if (empty($assignments)): ?>
                        <div class="text-center py-5 bg-white border rounded">
                            <i class="bi bi-journal-text fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No assignments created yet.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($assignments as $assignment): ?>
                        <div class="assignment-item shadow-sm">
                            <div class="assignment-icon">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><a href="view_assignment.php?id=<?php echo $assignment['id']; ?>" class="text-decoration-none"><?php echo $assignment['title']; ?></a></h6>
                                <small class="text-muted">Due: <?php echo date("F j, Y, g:i a", strtotime($assignment['due_date'])); ?></small>
                            </div>
                            <div class="text-end">
                                <?php if ($assignment['submitted_at']): ?>
                                    <span class="badge bg-success">Submitted</span><br>
                                    <?php if ($assignment['grade'] !== null): ?>
                                        <small class="text-muted">Grade: <strong><?php echo $assignment['grade']; ?>/100</strong></small>
                                    <?php else: ?>
                                        <small class="text-muted">Waiting for grade</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Assigned</span><br>
                                    <a href="view_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary btn-sm mt-2">Submit</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
