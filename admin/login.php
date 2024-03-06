<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $row = $stmt->fetch();

    if (!$row) {
        $_SESSION['error'] = 'Cannot find account with the username';
    } else {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['id'];
        } else {
            $_SESSION['error'] = 'Incorrect password';
        }
    }
} else {
    $_SESSION['error'] = 'Input admin credentials first';
}

$conn = null; // Fermeture de la connexion
header('location: index.php');
?>
