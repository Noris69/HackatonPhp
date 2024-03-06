<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];

    // Utilisation de requêtes préparées pour récupérer le votant
    $sql_select = "SELECT * FROM voters WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id); // "i" indique que le paramètre est un entier (id)
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $row = $result->fetch_assoc();

    if(password_verify($password, $row['password'])){
        // Utilisation de requêtes préparées pour mettre à jour les informations du votant
        $sql_update = "UPDATE voters SET firstname = ?, lastname = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $firstname, $lastname, $id); // "ssi" indique que les paramètres sont une chaîne, une chaîne et un entier respectivement
        if($stmt_update->execute()){
            $_SESSION['success'] = 'Voter updated successfully';
        }
        else{
            $_SESSION['error'] = $stmt_update->error;
        }
        $stmt_update->close();
    }
    else{
        $_SESSION['error'] = 'Incorrect password';
    }

    $stmt_select->close();
}
else{
    $_SESSION['error'] = 'Fill up edit form first';
}

header('location: voters.php');
?>
