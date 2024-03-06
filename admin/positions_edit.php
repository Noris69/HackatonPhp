<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$description = $_POST['description'];
		$max_vote = $_POST['max_vote'];
		$start_date = $_POST['start_date']; // Assuming this is obtained from the form
		$end_date = $_POST['end_date']; // Assuming this is obtained from the form

		// Utilisation de requêtes préparées pour mettre à jour la position
		$sql = "UPDATE positions SET description = ?, max_vote = ?, start_date = ?, end_date = ? WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("ssssi", $description, $max_vote, $start_date, $end_date, $id);
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
