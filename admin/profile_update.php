<?php
	include 'includes/session.php';

	if(isset($_GET['return'])){
		$return = $_GET['return'];
	}
	else{
		$return = 'home.php';
	}

	if(isset($_POST['save'])){
		$curr_password = $_POST['curr_password'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$photo = $_FILES['photo']['name'];
		
		// Vérification du mot de passe actuel
		$sql_check_password = "SELECT password FROM admin WHERE id = ?";
		$stmt = $conn->prepare($sql_check_password);
		if($stmt){
			$stmt->bind_param("i", $user['id']);
			$stmt->execute();
			$stmt->bind_result($hashed_password);
			$stmt->fetch();
			$stmt->close();

			if(password_verify($curr_password, $hashed_password)){
				if(!empty($photo)){
					move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$photo);
					$filename = $photo;	
				}
				else{
					$filename = $user['photo'];
				}

				if($password == $user['password']){
					$password = $user['password'];
				}
				else{
					$password = password_hash($password, PASSWORD_DEFAULT);
				}

				// Mise à jour du profil de l'administrateur
				$sql_update_admin = "UPDATE admin SET username = ?, password = ?, firstname = ?, lastname = ?, photo = ? WHERE id = ?";
				$stmt = $conn->prepare($sql_update_admin);
				if($stmt){
					$stmt->bind_param("sssssi", $username, $password, $firstname, $lastname, $filename, $user['id']);
					if($stmt->execute()){
						$_SESSION['success'] = 'Admin profile updated successfully';
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
				$_SESSION['error'] = 'Incorrect password';
			}
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up required details first';
	}

	header('location:'.$return);
?>
