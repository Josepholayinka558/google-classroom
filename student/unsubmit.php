<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if (isset($_GET['id']) && isset($_GET['assignment_id'])) {
    $submission_id = $_GET['id'];
    $assignment_id = $_GET['assignment_id'];
    $student_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM submissions WHERE id = ? AND student_id = ?");
    $stmt->execute([$submission_id, $student_id]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ?");
        $stmt->execute([$submission_id]);
        set_flash_message("Submission removed.");
    } else {
        set_flash_message("Unauthorized action.", "danger");
    }
}

redirect("view_assignment.php?id=$assignment_id");
?>
