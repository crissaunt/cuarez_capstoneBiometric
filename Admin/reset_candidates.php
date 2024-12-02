<?php
session_start();
include '../db/db_connection.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: login.php");
    exit();
}

// Ensure the request method is GET for this operation
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare the DELETE query to clear all rows from the 'candidates' table
        $query = "DELETE FROM `candidates`";
        $stmt = $conn->prepare($query);
        
        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the candidates page with a success message
            header("Location: candidates.php?success");
            exit();
        } else {
            throw new Exception("Error resetting candidates.");
        }
    } catch (Exception $e) {
        // Handle any errors
        echo "Error resetting candidates: " . $e->getMessage();
    }
} else {
    // If accessed using a different method (not GET)
    echo "Invalid request method.";
}
?>
