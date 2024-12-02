<?php
session_start();  // Start the session

// Unset specific session variables
unset($_SESSION['user']);  // Unset the 'user' session variable

// Destroy the entire session
session_destroy();  

// Redirect the user to the login page (or any other page)
header("Location: login.php");  
exit();  // Ensure no further code is executed
?>
