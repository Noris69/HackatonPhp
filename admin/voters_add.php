<?php
include 'includes/session.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['add'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $raw_password = $_POST['password']; // Mot de passe non hashé
    $password = password_hash($raw_password, PASSWORD_DEFAULT); // Hasher le mot de passe
    $email = $_POST['email']; // Récupération de l'adresse e-mail depuis le formulaire
    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);   
    }
    // Vérifier si l'e-mail existe déjà dans la base de données
    $email_check_query = "SELECT * FROM voters WHERE email='$email' LIMIT 1";
    $result = $conn->query($email_check_query);
    $user = $result->fetch_assoc();
    
    if ($user) { // Si l'e-mail existe déjà
        $_SESSION['error'] = 'Email already exists';
    } else {
        // Générer l'ID de l'électeur
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $voter_id = substr(str_shuffle($set), 0, 15);

        $sql = "INSERT INTO voters (voters_id, password, firstname, lastname, email, photo) VALUES ('$voter_id', '$password', '$firstname', '$lastname', '$email', '$filename')";
        if($conn->query($sql)){
            // Envoyer un e-mail avec PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true); // Initialisez la classe PHPMailer

            try {
                // Configuration de l'e-mail
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Entrez votre serveur SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'cheikh.noris69@gmail.com'; // Entrez votre adresse e-mail
                $mail->Password = 'eubv load dxux sdco'; // Entrez votre mot de passe
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                // Destinataire
                $mail->setFrom('your_email@example.com', $firstname . ' ' . $lastname); // Utiliser le prénom et le nom du nouvel électeur comme nom de l'expéditeur
                $mail->addAddress($email); // Adresse e-mail fournie dans le formulaire

                // Contenu de l'e-mail
                $mail->isHTML(true);
                $mail->Subject = 'Your Voter ID and Password';
                $mail->Body = 'Voter ID: ' . $voter_id . '<br>Password: ' . $raw_password; // Utiliser le mot de passe non hashé pour l'envoyer par e-mail

                // Envoyer l'e-mail
                $mail->send();

                $_SESSION['success'] = 'Voter added successfully and email sent';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = $conn->error;
        }
    }
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: voters.php');
?>
