<?php
include '../../db/db_connection.php'; 
session_start();
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First, retrieve the registrationNumber before deleting the record
    $selectSql = "SELECT registrationNumber FROM user_voter WHERE id = ?";
    
    if ($stmt = $conn->prepare($selectSql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $registrationNumber = $row['registrationNumber'];
            
            // Delete the record from the database
            $deleteSql = "DELETE FROM user_voter WHERE id = ?";
            if ($deleteStmt = $conn->prepare($deleteSql)) {
                $deleteStmt->bind_param("i", $id);

                if ($deleteStmt->execute()) {
                    // Construct the folder path
                    $folderPath = "../../images/" . $registrationNumber;

                    // Check if the folder exists and delete it
                    if (is_dir($folderPath)) {
                        // Delete folder and all contents inside (images)
                        array_map('unlink', glob("$folderPath/*.*")); // Delete files
                        rmdir($folderPath); // Delete folder itself
                    }

                    // Redirect to the voter page
                    header('Location: ../voter-page.php');
                    exit();
                } else {
                    echo "Error deleting record: " . $conn->error;
                }
                $deleteStmt->close();
            }
        } else {
            echo "Record not found.";
        }
        $stmt->close();
    } else {
        echo "Error preparing select statement: " . $conn->error;
    }
} else {
    echo "No ID specified.";
}

$conn->close();
?>
