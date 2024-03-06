<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];
		
		// Utilisation de requêtes préparées pour supprimer la position
		$sql = "DELETE FROM positions WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("i", $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Position deleted successfully';
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

	header('location: positions.php');
?>
