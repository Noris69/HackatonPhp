<?php
session_start();
include 'includes/conn.php';

if(isset($_POST['login'])){
    $voter = $_POST['voter'];
    $password = $_POST['password'];

    // Utilisation d'une requête préparée pour récupérer les informations du votant
    $sql = "SELECT id, password FROM voters WHERE voters_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $voter);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows < 1){
        $_SESSION['error'] = 'Cannot find voter with the ID';
    }
    else{
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['voter'] = $row['id'];
        }
        else{
            $_SESSION['error'] = 'Incorrect password';
        }
    }
    $stmt->close();
}
else{
    $_SESSION['error'] = 'Input voter credentials first';
}

header('location: index.php');
?>
