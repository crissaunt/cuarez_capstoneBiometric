<?php
include '../../db/db_connection.php'; 
session_start();
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../login.php");
    exit();
  }

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM positions WHERE id = ?"; 

  
    if ($stmt = $conn->prepare($sql)) {
     
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header('Location: ../position.php?deletesuccess');
            
        } else {
      
            echo "Error deleting record: " . $conn->error;
        }

     
        $stmt->close();
    } else {
     
        echo "Error preparing statement: " . $conn->error;
    }
} else {

    echo "No ID specified.";
}


$conn->close();
?>
