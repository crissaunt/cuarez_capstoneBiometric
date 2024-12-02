<?php
session_start();
include '../db/db_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['registerNumber'];

    // Check if the user has already voted
    $checkVoteStatus = $conn->prepare("SELECT has_voted FROM user_voter WHERE registrationNumber = ?");
    $checkVoteStatus->bind_param("s", $userId);
    $checkVoteStatus->execute();
    $checkVoteStatus->bind_result($hasVoted);
    $checkVoteStatus->fetch();
    $checkVoteStatus->close();

    if ($hasVoted === 'Yes') {
        // If already voted, show message and stop further processing
        echo '<script>
            alert("You have successfully voted!");
            window.location.href = "index.php";
        </script>';
        exit();
    }

    // Prepare to update vote status for the user
    $updateVoteStatus = $conn->prepare("UPDATE user_voter SET has_voted = 'Yes' WHERE registrationNumber = ?");
    if (!$updateVoteStatus) {
        die("Database error: " . $conn->error);
    }

    $updateVoteStatus->bind_param("s", $userId);

    // Begin transaction for atomic operations
    $conn->begin_transaction();

    try {
        // Execute the update for voting status
        if ($updateVoteStatus->execute()) {
            foreach ($_POST as $key => $value) {
                // Process only keys starting with 'position_'
                if (strpos($key, 'position_') === 0) {
                    $positionId = intval(substr($key, strlen('position_')));

                    if (is_array($value)) {
                        // Handle multiple candidates for positions allowing more than one vote
                        foreach ($value as $candidateId) {
                            $candidateId = intval($candidateId);

                            $insertVote = $conn->prepare("INSERT INTO votes (registrationNumber, candidate_id, position_id) VALUES (?, ?, ?)");
                            if (!$insertVote) {
                                throw new Exception("Database error: " . $conn->error);
                            }
                            $insertVote->bind_param("sis", $userId, $candidateId, $positionId);
                            $insertVote->execute();
                            $insertVote->close();
                        }
                    } else {
                        // Handle single candidate for positions allowing only one vote
                        $candidateId = intval($value);

                        $insertVote = $conn->prepare("INSERT INTO votes (registrationNumber, candidate_id, position_id) VALUES (?, ?, ?)");
                        if (!$insertVote) {
                            throw new Exception("Database error: " . $conn->error);
                        }
                        $insertVote->bind_param("sis", $userId, $candidateId, $positionId);
                        $insertVote->execute();
                        $insertVote->close();
                    }
                }
            }

            // Commit transaction
            $conn->commit();

            // Use JavaScript to alert and redirect
            echo '<script>
                alert("You have successfully voted!");
                window.location.href = "index.php";
            </script>';
            $updateVoteStatus->close();
            exit();
        } else {
            throw new Exception("Error updating vote status: " . $updateVoteStatus->error);
        }
    } catch (Exception $e) {
        // Rollback transaction in case of errors
        $conn->rollback();
        echo '<script>alert("An error occurred: ' . $e->getMessage() . '");</script>';
    }

    $updateVoteStatus->close();
}

$conn->close();
?>
