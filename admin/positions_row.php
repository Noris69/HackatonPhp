<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];

		// Utilisation de requêtes préparées pour sélectionner la position
		$sql = "SELECT * FROM positions WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			echo json_encode($row);
			$stmt->close();
		} else {
			echo json_encode(['error' => 'Error executing the query']);
		}
	}
?>
