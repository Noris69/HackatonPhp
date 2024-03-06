<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $description = $_POST['description'];
    $max_vote = $_POST['max_vote'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "SELECT * FROM positions ORDER BY priority DESC LIMIT 1";
    $query = $conn->query($sql);
    $row = $query->fetch_assoc();
    $priority = $row['priority'] + 1;

    // Utilisation de requêtes préparées pour l'insertion
    $sql = "INSERT INTO positions (description, max_vote, start_date, end_date, priority) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if($stmt){
        $stmt->bind_param("sissi", $description, $max_vote, $start_date, $end_date, $priority);
        if($stmt->execute()){
            $_SESSION['success'] = 'Position added successfully';
        }
        else{
            $_SESSION['error'] = $stmt->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = $conn->error;
    }

}
else{
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: positions.php');
?>
