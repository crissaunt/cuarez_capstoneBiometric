<?php
session_start();
include '../db/db_connection.php';

// Redirect if user is already logged in
if (isset($_SESSION['registeredNumber'])) {
    header("Location: index.php");
    exit();
}

// Initialize an error message variable
$errorMessage = null;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registerNumber = trim($_POST['registerNumber']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($registerNumber) || empty($password)) {
        $errorMessage = "All fields are required.";
    } else {
        try {
            // Prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, password FROM user_voter WHERE registrationNumber = ?");
            $stmt->bind_param("s", $registerNumber);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Check if the user has already voted
                $voteCheckStmt = $conn->prepare("SELECT id FROM votes WHERE registrationNumber = ?");
                $voteCheckStmt->bind_param("s", $registerNumber);
                $voteCheckStmt->execute();
                $voteCheckResult = $voteCheckStmt->get_result();

                if ($voteCheckResult->num_rows > 0) {
                    $errorMessage = "You have already voted. You can Only View the Dashboard.";
                } else {
                    // Verify the password
                    if (password_verify($password, $row['password'])) {
                        // Set session variables
                        $_SESSION['registeredNumber'] = $row['id'];
                        $_SESSION['login_message'] = "Login successful!";
                        header("Location: index.php");
                        exit();
                    } else {
                        $errorMessage = "Invalid login credentials.";
                    }
                }
            } else {
                $errorMessage = "Invalid login credentials.";
            }
        } catch (Exception $e) {
            $errorMessage = "An error occurred. Please try again.";
        } finally {
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!-- HTML Form remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/login.css">

</head>
<body>



    <header class="header">
        <div class="flex">
            <a href="#" class="logo"><img src="../img/image.png" alt="Logo" width="200px"></a>
            <div class="icons">
                <a href="dashboard.php"><button class="button-37">Dashboard</button></a>
                <a href="../register.html"><button class="button-37">Register</button></a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="login-box">
            <h1>Login</h1>
            <?php if (isset($errorMessage)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <label for="registerNumber">ID Number</label>
                <input 
                    type="text" 
                    id="registerNumber" 
                    name="registerNumber" 
                    placeholder="Enter your ID number" 
                    value="<?php echo isset($registerNumber) ? htmlspecialchars($registerNumber) : ''; ?>" 
                    required 
                />
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required 
                />
                <input type="submit" value="Submit" />
            </form>
            <p class="para-2">
                Don't have an account? <a href="../register.html" style="color: blue">Sign Up Here</a>
            </p>
        </div>
    </div>
</body>
</html>
