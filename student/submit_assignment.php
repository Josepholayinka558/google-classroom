<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_SESSION['user_id'];

    // Verify student is enrolled in the class for this assignment
    $stmt = $pdo->prepare("SELECT a.id FROM assignments a JOIN classes c ON a.class_id = c.id JOIN enrollments e ON c.id = e.class_id WHERE a.id = ? AND e.student_id = ?");
    $stmt->execute([$assignment_id, $student_id]);
    
    if ($stmt->fetch()) {
        // Handle file upload
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/submissions/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['submission_file']['name'];
            $file_path = 'uploads/submissions/' . $file_name;
            
            if (move_uploaded_file($_FILES['submission_file']['tmp_name'], '../' . $file_path)) {
                // Check if already submitted (if so, update)
                $stmt = $pdo->prepare("SELECT id FROM submissions WHERE assignment_id = ? AND student_id = ?");
                $stmt->execute([$assignment_id, $student_id]);
                $submission = $stmt->fetch();

                if ($submission) {
                    $stmt = $pdo->prepare("UPDATE submissions SET file_path = ?, submitted_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$file_path, $submission['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
                    $stmt->execute([$assignment_id, $student_id, $file_path]);
                }
                
                set_flash_message("Assignment turned in successfully!");
            } else {
                set_flash_message("Failed to upload file.", "danger");
            }
        } else {
            set_flash_message("No file attached.", "danger");
        }
    } else {
        set_flash_message("Unauthorized action.", "danger");
    }
    
    redirect("view_assignment.php?id=$assignment_id");
}
?>
