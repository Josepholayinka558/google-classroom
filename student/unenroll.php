<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if (isset($_GET['id'])) {
    $class_id = $_GET['id'];
    $student_id = $_SESSION['user_id'];

    // Unenroll student
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE student_id = ? AND class_id = ?");
    $stmt->execute([$student_id, $class_id]);
    set_flash_message("Unenrolled from class.");
}

redirect('dashboard.php');
?>
