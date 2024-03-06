<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    // Utilisation de requêtes préparées pour supprimer un votant
    $sql = "DELETE FROM voters WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // "i" indique que le paramètre est un entier (id)
    if($stmt->execute()){
        $_SESSION['success'] = 'Voter deleted successfully';
    }
    else{
        $_SESSION['error'] = $stmt->error;
    }
    $stmt->close();
}
else{
    $_SESSION['error'] = 'Select item to delete first';
}

header('location: voters.php');
?>
