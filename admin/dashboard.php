<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('admin');

// Get statistics
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$class_count = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$assignment_count = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
$submission_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();

// Get recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Get recent classes
$recent_classes = $pdo->query("SELECT c.*, u.name as teacher_name FROM classes c JOIN users u ON c.teacher_id = u.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();

$page_title = "Admin Dashboard";
include '../includes/header.php';
?>

<h2 class="mb-4">Admin Overview</h2>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-primary text-white h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-people fs-1 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0"><?php echo $user_count; ?></h3>
                <p class="mb-0 small">Total Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-success text-white h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-mortarboard fs-1 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0"><?php echo $class_count; ?></h3>
                <p class="mb-0 small">Total Classes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-info text-white h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-journal-text fs-1 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0"><?php echo $assignment_count; ?></h3>
                <p class="mb-0 small">Total Assignments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-warning text-dark h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-file-earmark-arrow-up fs-1 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0"><?php echo $submission_count; ?></h3>
                <p class="mb-0 small">Total Submissions</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Users</h5>
                <a href="manage_users.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'teacher' ? 'bg-primary' : 'bg-secondary'); ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                    <td class="small text-muted"><?php echo date("M j, Y", strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Classes</h5>
                <a href="manage_classes.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Teacher</th>
                                <th>Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_classes as $class): ?>
                                <tr>
                                    <td><?php echo $class['class_name']; ?></td>
                                    <td class="small"><?php echo $class['teacher_name']; ?></td>
                                    <td><code class="text-primary"><?php echo $class['class_code']; ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
