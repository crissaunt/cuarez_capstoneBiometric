<?php
session_start();
include '../db/db_connection.php';

// Redirect if user is already logged in
if (isset($_SESSION['registeredNumber'])) {
    header("Location: dashboard.php");
    exit();
}

// Initialize an error message variable
$errorMessage = null;

// Process login if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $registerNumber = trim($_POST['registerNumber']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($registerNumber) || empty($password)) {
        $errorMessage = "ID Number and Password are required!";
    } else {
        // Query to check if the ID exists
        $sql = "SELECT id, user, password FROM admin WHERE user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $registerNumber);
        $stmt->execute();
        $stmt->store_result();
        
        // Check if the user exists
        if ($stmt->num_rows === 1) {
            // Bind result variables
            $stmt->bind_result($id, $user, $hashedPassword);
            $stmt->fetch();
            
            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Password is correct, start session and set session variables
                $_SESSION['registeredNumber'] = $registerNumber;
                $_SESSION['user'] = $user;
                header("Location: dashboard.php");
                exit();
            } else {
                $errorMessage = "Incorrect password!";
            }
        } else {
            $errorMessage = "ID Number not found!";
        }

        $stmt->close();
    }

    $conn->close();
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
    
                <a href="register.php"><button class="button-37">Register</button></a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="login-box">
            <h1>Login Admin</h1>
            <?php if (isset($errorMessage)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <label for="registerNumber">Username</label>
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
                Don't have an account? <a href="register.php" style="color: blue">Sign Up Here</a>
            </p>
        </div>
    </div>
</body>
</html>
