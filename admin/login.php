<?php
session_start();
include 'includes/conn.php';

// Génération du jeton CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['login'])) {
    // Vérifier si la clé csrf_token est définie dans $_POST
    if (!isset($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Missing CSRF token';
        header('location: index.php');
        exit;
    }

    // Validation du jeton CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('location: index.php');
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find account with the username';
    } else {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['id'];
        } else {
            $_SESSION['error'] = 'Incorrect password';
        }
    }
} else {
    $_SESSION['error'] = 'Input admin credentials first';
}

$stmt->close();
$conn->close();

header('location: index.php');
?>
