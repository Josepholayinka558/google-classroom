<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        // Redirect based on current role if trying to access unauthorized area
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: /admin/dashboard.php");
                break;
            case 'teacher':
                header("Location: /teacher/dashboard.php");
                break;
            case 'student':
                header("Location: /student/dashboard.php");
                break;
            default:
                header("Location: /index.php");
        }
        exit();
    }
}

function get_user_data($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
?>
