<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $output = array('error'=>false);

    // Sélectionner la position spécifiée par l'ID
    $sql = "SELECT * FROM positions WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $priority = $row['priority'] - 1;

    if($priority == 0){
        $output['error'] = true;
        $output['message'] = 'This position is already at the top';
    }
    else{
        // Incrémenter la priorité des positions inférieures
        $sql = "UPDATE positions SET priority = priority + 1 WHERE priority = :priority";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':priority', $priority);
        $stmt->execute();

        // Mettre à jour la priorité de la position spécifiée par l'ID
        $sql = "UPDATE positions SET priority = :priority WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    echo json_encode($output);
}
?>
