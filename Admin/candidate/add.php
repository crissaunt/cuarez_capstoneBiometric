<?php
include '../../db/db_connection.php';

session_start();

if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize the form values
    $position_id = intval($_POST['position_id']);  
    $year_level = intval($_POST['year_level']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $platform = trim($_POST['platform']);
    $program = trim($_POST['program']);

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['image']['name']);
        $file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $file_name);  // Sanitize file name
        
        // Construct the folder path using both first and last names
        $folderPath = '../../img/' . $first_name . '_' . $last_name;
        $fullFilePath = $folderPath . '/' . $file_name;

        // Create the directory if it doesn't exist
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Validate file type (allow only images)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            // Move the uploaded file to the destination
            if (move_uploaded_file($_FILES['image']['tmp_name'], $fullFilePath)) {
                // Prepare the SQL query to insert the candidate
                $sql = "INSERT INTO candidates (first_name, last_name, platform, program, year_level, position_id, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind the parameters correctly
                    $stmt->bind_param('ssssiss', $first_name, $last_name, $platform, $program, $year_level, $position_id, $file_name);

                    // Execute the statement
                    if ($stmt->execute()) {
                        header('Location: ../candidates.php?addsuccess');
                        exit();
                    } else {
                        echo "Error executing query: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "File upload failed. Please try again.";
            }
        } else {
            echo "Invalid file type. Only JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        echo "No file uploaded or an error occurred during upload.";
    }

    $conn->close();
}
?>
