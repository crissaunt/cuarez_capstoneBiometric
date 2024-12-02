<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db/db_connection.php'; // Ensure your database connection is established

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registrationNumber = $_POST['registrationNumber'] ?? '';
    $positionId = $_POST['position_id'] ?? '';
    $candidateId = $_POST['candidate_id'] ?? '';

    // Validate the input
    if (!empty($registrationNumber) && !empty($positionId) && !empty($candidateId)) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO votes (registration_number, position_id, candidate_id) VALUES (?, ?, ?)");
        
        if ($stmt) { // Check if the statement was prepared successfully
            $stmt->bind_param("sii", $registrationNumber, $positionId, $candidateId);
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "Vote submitted successfully!";
            } else {
                echo "Error submitting vote: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error; // Handle statement preparation error
        }
    } else {
        echo "All fields are required.";
    }
}

// Close the database connection
$conn->close();
?>
