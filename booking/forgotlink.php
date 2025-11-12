<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Include your database connection
require '../includes/initialize.php'; // Make sure this file initializes your database connection

if (isset($_POST['reset_request'])) {
    $username = trim($_POST['username']); // Change to username as per your request

    // Validate username
    if (empty($username)) {
        echo "Please enter your username.";
    } else {
        // Check if the username exists in the database
        $query = "SELECT * FROM tblguest WHERE G_UNAME='$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            $expDate = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Update the token and expiration date in the database
            mysqli_query($conn, "UPDATE tblguest SET VERIFICATION_TOKEN='$token', OTP_EXPIRE_AT='$expDate' WHERE G_UNAME='$username'");

            // Send the email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'mr.renzs2024@gmail.com'; // Your email
            $mail->Password = 'qwzi vmiv vrkp uzma'; // Your email password
            $mail->Port = 587; // SMTP port
            $mail->setFrom('mr.renzs2024@gmail.com', 'Hotel Reservation');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link to reset your password: <a href='https://mcchmhotelreservation.com/booking/reset_password.php?token=$token&email=$email'>Reset Password</a>";

            if ($mail->send()) {
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Sent',
                            text: 'A password reset link has been sent to your email.',
                        });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Mailer Error',
                            text: 'Mailer Error: " . $mail->ErrorInfo . "',
                        });
                      </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Username Not Found',
                        text: 'No user found with that username.',
                    });
                  </script>";
        }
    }
}
?>