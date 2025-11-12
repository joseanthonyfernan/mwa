<?php
session_start();

if (isset($_POST['otp']) && isset($_POST['email'])) {
    $userOtp = filter_var($_POST['otp'], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the OTP exists in the session
    if (isset($_SESSION['otp']) && $_SESSION['otp_email'] == $email) {
        // Validate OTP format (6 digits)
        if (preg_match('/^[0-9]{6}$/', $userOtp)) {
            // Check if the OTP matches the session-stored OTP
            if ($userOtp == $_SESSION['otp']) {
                // OTP is valid
                echo 'valid';
            } else {
                // OTP is invalid
                echo 'invalid';
            }
        } else {
            echo 'Invalid OTP code'; // Handle the case where the user-input OTP is not a valid OTP code
        }
    } else {
        echo 'Email address does not match the OTP email address or OTP session expired';
    }
}
?>
