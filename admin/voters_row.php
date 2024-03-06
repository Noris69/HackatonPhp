<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];

		// Utilisation de requêtes préparées pour récupérer les informations du votant
		$sql = "SELECT * FROM voters WHERE id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id); // "i" indique que le paramètre est un entier
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		echo json_encode($row);

		$stmt->close();
	}
?>
