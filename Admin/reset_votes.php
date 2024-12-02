<?php
session_start();
include '../db/db_connection.php'; // Include database connection

if (!isset($_SESSION['registeredNumber'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare the DELETE query
        $query = "DELETE FROM `votes`"; // Clear all rows from the table
        $stmt = $conn->prepare($query);
        if ($stmt->execute()) {
            // Redirect to the page with a success message
            header("Location: votes-page.php?success");
            exit();
        } else {
            throw new Exception("Error resetting votes.");
        }
    } catch (Exception $e) {
        // Handle error
        echo "Error resetting votes: " . $e->getMessage();
    }
} else {
    // If accessed directly without GET request
    echo "Invalid request method.";
}
?>
