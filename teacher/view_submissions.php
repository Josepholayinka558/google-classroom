<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$assignment_id = $_GET['id'];
$teacher_id = $_SESSION['user_id'];

// Get assignment info and verify ownership
$stmt = $pdo->prepare("SELECT a.*, c.class_name FROM assignments a JOIN classes c ON a.class_id = c.id WHERE a.id = ? AND c.teacher_id = ?");
$stmt->execute([$assignment_id, $teacher_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    redirect('dashboard.php');
}

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {
    $submission_id = $_POST['submission_id'];
    $grade = sanitize($_POST['grade']);
    $feedback = sanitize($_POST['feedback']);

    $stmt = $pdo->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?");
    $stmt->execute([$grade, $feedback, $submission_id]);
    set_flash_message("Submission graded successfully!");
    redirect("view_submissions.php?id=$assignment_id");
}

// Get all submissions for this assignment
$stmt = $pdo->prepare("SELECT s.*, u.name, u.email FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.assignment_id = ? ORDER BY s.submitted_at DESC");
$stmt->execute([$assignment_id]);
$submissions = $stmt->fetchAll();

$page_title = "Submissions: " . $assignment['title'];
include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="view_class.php?id=<?php echo $assignment['class_id']; ?>"><?php echo $assignment['class_name']; ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $assignment['title']; ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Submissions for "<?php echo $assignment['title']; ?>"</h2>
    <span class="badge bg-primary fs-6"><?php echo count($submissions); ?> Submitted</span>
</div>

<?php if (empty($submissions)): ?>
    <div class="text-center py-5 bg-white border rounded">
        <i class="bi bi-file-earmark-arrow-up fs-1 text-muted"></i>
        <p class="text-muted mt-3">No submissions yet for this assignment.</p>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($submissions as $submission): ?>
            <div class="col-md-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle fs-2 me-3 text-primary"></i>
                                    <div>
                                        <h6 class="mb-0"><?php echo $submission['name']; ?></h6>
                                        <small class="text-muted"><?php echo $submission['email']; ?></small><br>
                                        <small class="text-muted">Submitted: <?php echo date("F j, Y, g:i a", strtotime($submission['submitted_at'])); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo $base_url . '/' . $submission['file_path']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download me-1"></i> Download Submission
                                </a>
                            </div>
                            <div class="col-md-5">
                                <?php if ($submission['grade'] === null): ?>
                                    <form action="view_submissions.php?id=<?php echo $assignment_id; ?>" method="POST" class="row g-2 align-items-center">
                                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                        <input type="hidden" name="grade_submission" value="1">
                                        <div class="col-3">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="grade" placeholder="Grade" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="form-control form-control-sm" name="feedback" placeholder="Feedback (Optional)">
                                        </div>
                                        <div class="col-3">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">Submit</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-success">Graded: <?php echo $submission['grade']; ?>/100</span>
                                            <p class="small text-muted mb-0 mt-1">Feedback: <?php echo $submission['feedback'] ?: 'No feedback'; ?></p>
                                        </div>
                                        <button class="btn btn-link btn-sm" onclick="this.parentElement.parentElement.innerHTML = document.getElementById('grade-form-<?php echo $submission['id']; ?>').innerHTML">Edit</button>
                                    </div>
                                    <div id="grade-form-<?php echo $submission['id']; ?>" class="d-none">
                                        <form action="view_submissions.php?id=<?php echo $assignment_id; ?>" method="POST" class="row g-2 align-items-center">
                                            <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                            <input type="hidden" name="grade_submission" value="1">
                                            <div class="col-3">
                                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="grade" value="<?php echo $submission['grade']; ?>" required>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control form-control-sm" name="feedback" value="<?php echo $submission['feedback']; ?>">
                                            </div>
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
