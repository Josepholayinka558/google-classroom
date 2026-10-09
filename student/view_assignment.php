<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$assignment_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

// Get assignment info and verify student enrollment
$stmt = $pdo->prepare("SELECT a.*, c.class_name, c.id as class_id FROM assignments a JOIN classes c ON a.class_id = c.id JOIN enrollments e ON c.id = e.class_id WHERE a.id = ? AND e.student_id = ?");
$stmt->execute([$assignment_id, $student_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    redirect('dashboard.php');
}

// Check for submission
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE assignment_id = ? AND student_id = ?");
$stmt->execute([$assignment_id, $student_id]);
$submission = $stmt->fetch();

$page_title = $assignment['title'];
include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-3">
            <div class="assignment-icon me-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <h1 class="mb-0 fs-2"><?php echo $assignment['title']; ?></h1>
                <p class="text-muted mb-0">Due: <?php echo date("F j, Y, g:i a", strtotime($assignment['due_date'])); ?></p>
            </div>
        </div>
        <hr>
        <div class="mt-4">
            <p class="lead"><?php echo nl2br($assignment['description']); ?></p>
            <?php if ($assignment['file_path']): ?>
                <div class="card bg-light mt-3">
                    <div class="card-body py-2">
                        <a href="<?php echo $base_url . '/' . $assignment['file_path']; ?>" target="_blank" class="text-decoration-none">
                            <i class="bi bi-file-earmark-text me-2"></i> View Attached Material
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Your Work</h5>
                    <span class="badge <?php echo $submission ? 'bg-success' : 'bg-warning text-dark'; ?>">
                        <?php echo $submission ? 'Turned In' : 'Assigned'; ?>
                    </span>
                </div>

                <?php if ($submission): ?>
                    <div class="mb-3">
                        <div class="p-2 border rounded d-flex align-items-center mb-2">
                            <i class="bi bi-file-earmark-check fs-4 me-2 text-success"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 small text-truncate"><?php echo basename($submission['file_path']); ?></p>
                            </div>
                        </div>
                        <?php if ($submission['grade'] !== null): ?>
                            <div class="alert alert-info py-2 small mt-3">
                                <strong>Grade: <?php echo $submission['grade']; ?>/100</strong><br>
                                <?php if ($submission['feedback']): ?>
                                    <span class="text-muted">Feedback: <?php echo $submission['feedback']; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="unsubmit.php?id=<?php echo $submission['id']; ?>&assignment_id=<?php echo $assignment_id; ?>" class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('Are you sure you want to unsubmit?')">Unsubmit</a>
                <?php else: ?>
                    <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                        <div class="mb-3">
                            <label for="submission_file" class="form-label small">Attach File</label>
                            <input type="file" class="form-control form-control-sm" id="submission_file" name="submission_file" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Turn In</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
