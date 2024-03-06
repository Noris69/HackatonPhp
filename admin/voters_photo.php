<?php
include 'includes/session.php';

if(isset($_POST['upload'])){
    $id = $_POST['id'];
    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);
        
        // Utilisation de requêtes préparées pour mettre à jour la photo du votant
        $sql = "UPDATE voters SET photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $filename, $id); // "si" indique que les paramètres sont une chaîne et un entier respectivement
        if($stmt->execute()){
            $_SESSION['success'] = 'Photo updated successfully';
        }
        else{
            $_SESSION['error'] = $stmt->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Select voter to update photo first';
    }
}
else{
    $_SESSION['error'] = 'Select voter to update photo first';
}

header('location: voters.php');
?>
