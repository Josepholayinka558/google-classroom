<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

if (isset($_GET['id']) && isset($_GET['class_id'])) {
    $announcement_id = $_GET['id'];
    $class_id = $_GET['class_id'];
    $teacher_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT a.id FROM announcements a JOIN classes c ON a.class_id = c.id WHERE a.id = ? AND c.teacher_id = ?");
    $stmt->execute([$announcement_id, $teacher_id]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$announcement_id]);
        set_flash_message("Announcement deleted!");
    } else {
        set_flash_message("Unauthorized action.", "danger");
    }
}

redirect("view_class.php?id=$class_id");
?>
