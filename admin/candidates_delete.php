<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];

		// Utilisation de requêtes préparées
		$sql = "DELETE FROM candidates WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("i", $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Candidate deleted successfully';
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
		$_SESSION['error'] = 'Select item to delete first';
	}

	header('location: candidates.php');
?>
