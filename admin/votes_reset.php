<?php
include 'includes/session.php';

// Utilisation d'une requête préparée pour supprimer toutes les données de la table "votes"
$sql = "DELETE FROM votes";
$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    $_SESSION['success'] = "Votes reset successfully";
} else {
    $_SESSION['error'] = "Something went wrong in resetting";
}

$stmt->close();
header('location: votes.php');
?>
