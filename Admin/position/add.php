<?php
    include '../../db/db_connection.php'; 
    session_start();
    if (!isset($_SESSION['registeredNumber'])) {
        header("Location: ../login.php");
        exit();
      }

    if($_SERVER['REQUEST_METHOD']==='POST'){

    $position = $_POST['position'];
    $max_votes = $_POST['max_votes'];
    $priority = $_POST['priority'];

    $sql = "INSERT INTO positions (position_name, max_vote,priority) VALUES (?,?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $position, $max_votes,$priority);

    if($stmt->execute()){
        echo "New Position Added Successfully";
    }else{
        echo "Error" . $stmt->error;
    }

  

    header('Location: ../position.php?addsuccess');
    
    $stmt->close();
    $conn->close();

    exit();
}

?>