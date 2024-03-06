<?php
include 'includes/session.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['add'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $raw_password = generateRandomPassword(); // Generate a random password
    $password = password_hash($raw_password, PASSWORD_DEFAULT); // Hash the generated password
    $email = $_POST['email']; // Get email from the form
    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);   
    }
    // Check if email already exists in the database using prepared statements
    $email_check_query = "SELECT * FROM voters WHERE email=? LIMIT 1";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user) { // If email already exists
        $_SESSION['error'] = 'Email already exists';
    } else {
        // Generate voter ID
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $voter_id = substr(str_shuffle($set), 0, 15);

        // Insert new voter into the database using prepared statements
        $sql = "INSERT INTO voters (voters_id, password, firstname, lastname, email, photo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $voter_id, $password, $firstname, $lastname, $email, $filename);
        if($stmt->execute()){
            // Send email using PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true); // Initialize PHPMailer

            try {
                // Email configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Enter your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'cheikh.noris69@gmail.com'; // Enter your email address
                $mail->Password = 'eubv load dxux sdco'; // Enter your password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                // Recipient
                $mail->setFrom('your_email@example.com', $firstname . ' ' . $lastname); // Use the first name and last name of the new voter as sender name
                $mail->addAddress($email); // Email address provided in the form

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Your Voter ID and Password';
                $mail->Body = 'Voter ID: ' . $voter_id . '<br>Password: ' . $raw_password; // Use the raw password to send via email

                // Send email
                $mail->send();

                $_SESSION['success'] = 'Voter added successfully and email sent';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = $stmt->error;
        }
        $stmt->close();
    }
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: voters.php');

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>
