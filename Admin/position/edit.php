<?php
include '../../db/db_connection.php';

session_start();
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../login.php");
    exit();
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['position'], $_POST['max_vote'], $_POST['priority'])) {
        $id = $_POST['id'];
        $position = $_POST['position'];
        $max_vote = $_POST['max_vote'];
        $priority = $_POST['priority'];

        // Update the position record
        $sql = "UPDATE positions SET position_name = ?, max_vote = ?, priority = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Check if prepare failed
        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
            exit();
        }

        // Bind the parameters
        $stmt->bind_param('siii', $position, $max_vote, $priority, $id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Position updated successfully!";
            header('Location: ../position.php?updatesuccess'); // Redirect after successful update
            exit();
        } else {
            echo "Error updating position: " . $stmt->error;
        }

        // Close the prepared statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Required fields are missing.";
    }
}
?>
