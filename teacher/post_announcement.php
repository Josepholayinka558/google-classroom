<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $content = sanitize($_POST['content']);
    $teacher_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $teacher_id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO announcements (class_id, content) VALUES (?, ?)");
        $stmt->execute([$class_id, $content]);
        set_flash_message("Announcement posted!");
    } else {
        set_flash_message("Unauthorized action.", "danger");
    }
    
    redirect("view_class.php?id=$class_id");
}
?>
