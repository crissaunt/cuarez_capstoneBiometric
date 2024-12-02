<?php
include '../../db/db_connection.php'; 
session_start();
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // Ensure ID is an integer

    // First, retrieve candidate details (first_name, last_name, image)
    $selectSql = "SELECT first_name, last_name, image FROM candidates WHERE id = ?";
    
    if ($stmt = $conn->prepare($selectSql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($candidate = $result->fetch_assoc()) {
            $first_name = $candidate['first_name'];
            $last_name = $candidate['last_name'];
            $image = $candidate['image'];

            // Delete the record from the database
            $deleteSql = "DELETE FROM candidates WHERE id = ?";
            if ($deleteStmt = $conn->prepare($deleteSql)) {
                $deleteStmt->bind_param("i", $id);

                if ($deleteStmt->execute()) {
                    // Construct the folder path and image path
                    $folderPath = '../../img/' . $first_name . '_' . $last_name;
                    $imagePath = $folderPath . '/' . $image;

                    // Check if the image file exists and delete it
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }

                    // Optionally, delete the folder if empty
                    if (is_dir($folderPath) && count(scandir($folderPath)) == 2) { // '.' and '..'
                        rmdir($folderPath);
                    }

                    // Redirect after successful deletion
                    header('Location: ../candidates.php?deletesuccess');
                    exit();
                } else {
                    echo "Error deleting record: " . $deleteStmt->error;
                }
                $deleteStmt->close();
            }
        } else {
            echo "Candidate not found.";
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
