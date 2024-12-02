<?php
include '../../db/db_connection.php'; 
session_start();


if (!isset($_SESSION['registeredNumber'])) {
    header("Location: ../index.php");
    exit();
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voter'])) {
    $id = $_POST['registrationNumber'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $program = $_POST['program'];
    $registration_date = date("Y-m-d H:i:s");

    $studentimage1 = $_POST['studentimage1'];
    $studentimage2 = $_POST['studentimage2'];

    // Validate email & registrationNumber uniqueness
    $check_sql = "SELECT * FROM user_voter WHERE registrationNumber = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ss', $id, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Registration Number or Email already exists.');
        window.location.href='../voter-page.php';</script>";
        
        exit();
    }

    // Decode base64 images 
    $base64Data1 = explode(',', $studentimage1)[1];
    $base64Data2 = explode(',', $studentimage2)[1];
    $imageData1 = base64_decode($base64Data1);
    $imageData2 = base64_decode($base64Data2);

    // Verify the image is valid
    if (!$imageData1 || !$imageData2) {
        echo "Invalid image data.";
        exit();
    }

    // Create folder path based on registration number
    $folderPath = "../../images/{$id}/";

    if (!file_exists($folderPath)) {
        if (!mkdir($folderPath, 0777, true)) {
            echo "Failed to create folder.";
            exit();
        }
    }

    // Save images to the folder
    $image1Path = $folderPath . "1.png";
    $image2Path = $folderPath . "2.png";

    if (!file_put_contents($image1Path, $imageData1) || !file_put_contents($image2Path, $imageData2)) {
        echo "Failed to save images.";
        exit();
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO user_voter (registrationNumber, first_name, last_name, gender, email, password, program, dateRegistered, studentImage1, studentImage2) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('ssssssssss', $id, $first_name, $last_name, $gender, $email, $hashed_password, $program, $registration_date, $image1Path, $image2Path);

        if ($stmt->execute()) {
            echo "New Voter Added Successfully";
            header('Location: ../voter-page.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
