<?php
// Include database connection
include '../../db/db_connection.php'; 
session_start();

if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_candidate'])) {
    // Sanitize and capture the input data from the 
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $platform = $_POST['platform'];
    $program = $_POST['program'];
    $year_level = $_POST['year_level'];
    $position_id = $_POST['position_id'];
    
    if (isset($_POST['position_id'])) {
        $position_id = $_POST['position_id'];
    } else {
        // Handle the error when position_id is missing
        echo "Position ID is missing.";
    }
    
    
    // Check if the 'id' is set (this should come from the URL or hidden field in the form)
  
        // Prepare the SQL statement to update the candidate's data
        $sql = "UPDATE candidates SET first_name = ?, last_name = ?, platform = ?, program = ?, year_level = ?, position_id = ? WHERE id = ?";
        
        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the parameters (the 'i' indicates an integer, 's' indicates a string)
            $stmt->bind_param('ssssssi', $first_name, $last_name, $platform, $program, $year_level, $position_id, $id);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to the candidates list page with a success message
                header("Location: ../candidates.php?updatesuccess=true");
                exit();
            } else {
                echo "Error updating candidate: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            echo "Error preparing the SQL statement: " . $conn->error;
        }
    } else {
        echo "Candidate ID is missing.";
    

    // Close the database connection
    $conn->close();
}
?>