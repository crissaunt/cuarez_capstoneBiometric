<?php
session_start();
include '../db/db_connection.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $email = trim($_POST['email']);
    $username = trim($_POST['user']);
    $password = trim($_POST['password']);

    $errorMessage = '';
    $successMessage = '';

    // Validate input
    if (empty($email) || empty($username) || empty($password)) {
        $errorMessage = 'All fields are required!';
    } else {
        // Check if the email or username already exists
        $checkEmailSql = "SELECT id FROM admin WHERE email = ? OR user = ?";
        $stmt = $conn->prepare($checkEmailSql);
        // Bind both email and username
        $stmt->bind_param("ss", $email, $username); // Note: "ss" for two string parameters
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errorMessage = "Email or Username already exists!";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin user into the database
            $insertSql = "INSERT INTO admin (email, user, password) VALUES (?, ?, ?)";
            
            if ($stmt = $conn->prepare($insertSql)) {
                $stmt->bind_param("sss", $email, $username, $hashedPassword);
                
                if ($stmt->execute()) {
                    $successMessage = "Registration successful! You can now <a href='login.php'>login</a>.";
                } else {
                    $errorMessage = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errorMessage = "Error preparing statement: " . $conn->error;
            }
        }
    
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
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/login.css">
</head>
<style>
    .error{
        color: red;
        font-weight: 550;
    }
</style>
<body>
    <header class="header">
        <div class="flex">
            <a href="#" class="logo"><img src="../img/image.png" alt="Logo" width="200px"></a>
            <div class="icons">
                <a href="login.php"><button class="button-37">Login</button></a>
            </div>
            
          
        </div>
    </header>

    <div class="container">
        <div class="login-box">
            <h1>Register Admin</h1>
              <!-- Display error or success message -->
              <?php if (isset($errorMessage)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
            <?php if (isset($successMessage)): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>

          
            <form action="" method="post" id="registerForm">
                <label for="user">Enter an email</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email" 
                />
                <div class="error" id="emailError"></div>

                <label for="registerNumber">Enter a Username</label>
                <input 
                    type="text" 
                    id="user" 
                    name="user" 
                    placeholder="Enter your username" 
                />
                <div class="error" id="userError"></div>
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
             
                />
                <div class="error" id="passwordError"></div>

                <label for="password">Confirm Password</label>
                <input 
                    type="password" 
                    id="repassword" 
                    name="repassword" 
                    placeholder="Enter your password again" 
                    
                />
                <div class="error" id="repasswordError"></div>

                <input type="submit" value="Submit" />
            </form>
            <p class="para-2">
              Have an account? <a href="login.php" style="color: blue">Sign in </a>
            </p>
        </div>
    </div>

    <script>
    function validateForm() {
        // Get form values
        const user = document.getElementById('user').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const repassword = document.getElementById('repassword').value;

        // Clear previous error messages
        document.getElementById('emailError').innerText = '';
        document.getElementById('userError').innerText = '';
        document.getElementById('passwordError').innerText = '';
        document.getElementById('repasswordError').innerText = '';

        // Initialize a flag for form validity
        let isValid = true;

        if (!user) {
        document.getElementById('userError').innerText = 'Username is required.';
        document.getElementById('user').style.borderColor = 'red'; // Red border for error
        isValid = false;
        } else {
            document.getElementById('user').style.borderColor = 'green'; // Green border for valid input
        }
        if (!email) {
            document.getElementById('emailError').innerText = 'Email is required.';
            document.getElementById('email').style.borderColor = 'red';
            isValid = false;
        } else {
            document.getElementById('email').style.borderColor = 'green';
        }
        if (!password) {
            document.getElementById('passwordError').innerText = 'Password is required.';
            document.getElementById('password').style.borderColor = 'red';
            isValid = false;
        } else {
            document.getElementById('password').style.borderColor = 'green';
        }
        if (!repassword) {
            document.getElementById('repasswordError').innerText = 'Re-enter password is required.';
            document.getElementById('repassword').style.borderColor = 'red';
            isValid = false;
        } else {
            document.getElementById('repassword').style.borderColor = 'green';
        }

        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (email && !emailPattern.test(email)) {
            document.getElementById('emailError').innerText = 'Please enter a valid email address.';
            document.getElementById('email').style.borderColor = 'red';
            isValid = false;
        }

        if (password !== repassword) {
        document.getElementById('repasswordError').innerText = 'Passwords do not match.';
        document.getElementById('repassword').style.borderColor = 'red';
        isValid = false;
        }

        const passwordStrengthPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        if (password && !passwordStrengthPattern.test(password)) {
            document.getElementById('passwordError').innerText = 'Password must be at least 8 characters long, with one uppercase letter, one number, and one special character.';
            document.getElementById('password').style.borderColor = 'red';
            isValid = false;
        }


        return isValid; // If any validation fails, return false and prevent form submission
    }

    // Attach the validateForm function to the form submit event
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        if (!validateForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
</script>



</body>
</html>
