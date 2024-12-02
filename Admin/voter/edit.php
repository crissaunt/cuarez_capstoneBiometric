<?php
include '../../db/db_connection.php'; 

session_start();
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_voter'])) {
    
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $program = $_POST['program'];
    $registration_date = date("Y-m-d H:i:s");

    if (!empty($password)) {
        // If the password field is not empty, hash the new password and update
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user_voter SET first_name = ?, last_name = ?, gender = ?, email = ?, password = ?, program = ?, dateRegistered = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $gender, $email, $hashed_password, $program, $registration_date, $id);
    } else {
        // If the password is empty, skip updating the password
        $stmt = $conn->prepare("UPDATE user_voter SET first_name = ?, last_name = ?, gender = ?, email = ?, program = ?, dateRegistered = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $gender, $email, $program, $registration_date, $id);
    }

    if ($stmt->execute()) {

        header('Location: ../voter-page.php'); // Redirect after success
        exit(); // Ensure no further code is executed
    } else {
        echo "Error updating voter: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
