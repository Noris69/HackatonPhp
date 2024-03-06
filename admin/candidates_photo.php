<?php
	include 'includes/session.php';

	if(isset($_POST['upload'])){
		$id = $_POST['id'];
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}
		
		// Utilisation de requêtes préparées
		$sql = "UPDATE candidates SET photo = ? WHERE id = ?";
		$stmt = $conn->prepare($sql);
		if($stmt){
			$stmt->bind_param("si", $filename, $id);
			if($stmt->execute()){
				$_SESSION['success'] = 'Photo updated successfully';
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
		$_SESSION['error'] = 'Select candidate to update photo first';
	}

	header('location: candidates.php');
?>
