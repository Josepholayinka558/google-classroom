<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (isset($_GET['id'])) {
    $class_id = $_GET['id'];
    $teacher_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $teacher_id]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        set_flash_message("Class deleted successfully!");
    } else {
        set_flash_message("Unauthorized action.", "danger");
    }
}

redirect('dashboard.php');
?>
