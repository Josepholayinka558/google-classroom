<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$class_id = $_GET['id'];
$teacher_id = $_SESSION['user_id'];

// Get class info
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$class_id, $teacher_id]);
$class = $stmt->fetch();

if (!$class) {
    redirect('dashboard.php');
}

// Get announcements
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE class_id = ? ORDER BY created_at DESC");
$stmt->execute([$class_id]);
$announcements = $stmt->fetchAll();

// Get assignments
$stmt = $pdo->prepare("SELECT * FROM assignments WHERE class_id = ? ORDER BY created_at DESC");
$stmt->execute([$class_id]);
$assignments = $stmt->fetchAll();

$page_title = $class['class_name'];
include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="class-card-header p-4 rounded shadow-sm d-flex flex-column justify-content-end" style="background-color: #1a73e8; background-image: url('https://www.gstatic.com/classroom/themes/Honors.jpg'); height: 200px;">
            <h1 class="text-white"><?php echo $class['class_name']; ?></h1>
            <p class="text-white lead mb-0"><?php echo $class['subject']; ?></p>
            <p class="text-white small mb-0 mt-2">Class Code: <span class="fw-bold bg-white text-primary px-2 py-1 rounded"><?php echo $class['class_code']; ?></span></p>
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
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Manage Class</h5>
                <div class="d-grid gap-2 mt-3">
                    <a href="create_assignment.php?class_id=<?php echo $class_id; ?>" class="btn btn-outline-primary btn-sm">Create Assignment</a>
                    <a href="view_students.php?class_id=<?php echo $class_id; ?>" class="btn btn-outline-secondary btn-sm">View Students</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Post Announcement Form -->
        <div class="announcement-box shadow-sm mb-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-circle fs-3 me-2 text-primary"></i>
                <h5 class="mb-0">Announce something to your class</h5>
            </div>
            <form action="post_announcement.php" method="POST">
                <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                <div class="mb-3">
                    <textarea class="form-control" name="content" rows="3" placeholder="Share with your class..." required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm px-4">Post</button>
                </div>
            </form>
        </div>

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
                            <p class="text-muted mt-3">No announcements yet. Post your first update!</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-box shadow-sm mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                    <div>
                                        <h6 class="mb-0"><?php echo $_SESSION['name']; ?></h6>
                                        <small class="text-muted"><?php echo date("F j, Y", strtotime($announcement['created_at'])); ?></small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item text-danger" href="delete_announcement.php?id=<?php echo $announcement['id']; ?>&class_id=<?php echo $class_id; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mb-0"><?php echo nl2br($announcement['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tab-pane fade" id="assignments">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="create_assignment.php?class_id=<?php echo $class_id; ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Create Assignment</a>
                    </div>
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
                                <h6 class="mb-0"><a href="view_submissions.php?id=<?php echo $assignment['id']; ?>" class="text-decoration-none"><?php echo $assignment['title']; ?></a></h6>
                                <small class="text-muted">Due: <?php echo date("F j, Y, g:i a", strtotime($assignment['due_date'])); ?></small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="view_submissions.php?id=<?php echo $assignment['id']; ?>">View Submissions</a></li>
                                    <li><a class="dropdown-item" href="edit_assignment.php?id=<?php echo $assignment['id']; ?>">Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="delete_assignment.php?id=<?php echo $assignment['id']; ?>&class_id=<?php echo $class_id; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
