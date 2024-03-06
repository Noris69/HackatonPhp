<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$description = $_POST['description'];
		$max_vote = $_POST['max_vote'];

		// Utilisation de requêtes préparées pour mettre à jour la position
		$sql = "UPDATE positions SET description = ?, max_vote = ? WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("ssi", $description, $max_vote, $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Position updated successfully';
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
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: positions.php');
?>
