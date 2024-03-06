<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];

		// Utilisation de requêtes préparées
		$sql = "SELECT *, candidates.id AS canid FROM candidates LEFT JOIN positions ON positions.id=candidates.position_id WHERE candidates.id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			echo json_encode($row);
			$stmt->close();
		} else {
			echo json_encode(array('error' => 'Database error'));
		}
	}
?>
