<?php
session_start();
include '../db/db_connection.php'; 

// Fetch user 
$userDetailsSql = "SELECT id, first_name, last_name, registrationNumber, studentImage1, studentImage2, dateRegistered, has_voted, password, email, gender FROM user_voter WHERE id = ?";
$userDetailsStmt = $conn->prepare($userDetailsSql);
if (!$userDetailsStmt) {
    die("Database error: " . $conn->error);
}
$userDetailsStmt->bind_param("i", $userId);
$userDetailsStmt->execute();
$userDetailsResult = $userDetailsStmt->get_result();

$userDetails = $userDetailsResult->fetch_assoc(); // Get user details

// positions table
$positionsSql = "SELECT * FROM positions";
$positionsResult = $conn->query($positionsSql);

if (!$positionsResult) {
    die("Error fetching positions: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Here!</title>
    <link rel="stylesheet" type="text/css" href="../backupCSS/style.css">
    <link rel="stylesheet" type="text/css" href="../backupCSS/modalstyle.css">
    <script src="../face-api.min.js"></script>

</head>
<body data-registered-number="<?php echo htmlspecialchars($userDetails['registrationNumber']); ?>">
<header class="header">
    <div class="flex">
        <a href="#" class="logo"><img src="../img/image.png" alt="" width="200px"></a>
        <nav class="navbar">
            <!-- Nav -->
        </nav>
        <div class="icons">
            <a href="#"><button class="button-37">View Your Vote</button></a>
            <a href="#"><button class="button-37">About Us</button></a>
        </div>
    </div>
</header>

<div class="form" >
    <form id="voteForm" method="POST" action="vote.php">
        <!-- <div class="title-div">
            <img src="../img/image.png" alt="">
            <p class="required">*Click the name to view their profile</p>
            <p class="description">This is description...</p>
        </div> -->
            <div class="title-div">
                <!-- <h5 class="electionTitle">CEIT ELECTION 2023</h5> -->
                <img src="../img/elect.png" alt="" class="electionTitle" >
            </div>  
            <div class="id-div">
          
          <div class="id">
          
              <label for="registerNumber">ID Number: </label>       
              <input type="text" id="userRegistrationNumber" name="registerNumber"   placeholder="Enter Your ID Number">
              <span class="required">This Field is Required </span>
          </div>
      
         </div>
        

            

        <?php
        if ($positionsResult->num_rows > 0) {
            while ($positionRow = $positionsResult->fetch_assoc()) {
                $positionId = htmlspecialchars($positionRow['id']);
                $positionName = htmlspecialchars($positionRow['position_name']);
                $positionmaxVote = htmlspecialchars($positionRow['max_vote']);

                echo '<div class="name-div">';
                echo '<div class="name"> ' . strtoupper($positionName) . '<span class="required">*</span>
              
                </div>';

                echo '  <div class="maxVote">';
                    if($positionmaxVote > 1){
                        echo '<p>Select only '.$positionmaxVote.' candidate </p>
                        
                        <button class="reset">Reset</button>

                        </div>';
                        

                    } else{
                         echo '<p>Select only '.$positionmaxVote.' candidate </p>
                        
                        <button class="reset">Reset</button>

                        </div>';
                
                    }
                   

                // Fetch candidates for the current position by position_id
                $candidatesSql = "SELECT id, first_name, last_name,program,year_level FROM candidates WHERE position_id = ?";
                $stmt = $conn->prepare($candidatesSql);
                if (!$stmt) {
                    die("Database error: " . $conn->error);
                }
                $stmt->bind_param("i", $positionId);
                $stmt->execute();
                $candidatesResult = $stmt->get_result();

                if ($candidatesResult->num_rows > 0) {
                    while ($candidateRow = $candidatesResult->fetch_assoc()) {
                        $candidateId = htmlspecialchars($candidateRow['id']);
                        $candidateName = htmlspecialchars($candidateRow['first_name'] . ' ' . $candidateRow['last_name']);
                        $candidateprogram = htmlspecialchars($candidateRow['program']);
                        $candidateyear_level = htmlspecialchars($candidateRow['year_level']);
                        
                        $input = ($positionmaxVote > 1) ? '<input type="checkbox" name="position_' . $positionId . '[]" value="' . $candidateId . '" >' : '<input type="radio" name="position_' . $positionId . '" value="' . $candidateId . '" required>';
                        echo '<div class="input-div">';
                    
                        echo '<input type="radio" name="position_' . $positionId . '" value="' . $candidateId . '" >';

                        // $candidateImage = htmlspecialchars($candidateRow['candidate_image'] ?? 'default.png');
                        // echo '<img src="../img/' . $candidateImage . '" alt="Candidate Image" width="125" height="125">';
                        
                        echo '
                        <img src="../img/richard.png" alt="Candidate Image" width="125" height="125px">
                        <div class="candidateInfo">
                        
                        <label>' . $candidateName . '</label>
                            <p>' . $candidateprogram . $candidateyear_level . '</p>
                            <button >See more... </button>
                        </div>   
                        
                        </div>';
                    }
                } else {
                    echo '<p>No candidate.</p>';
                }

                echo '</div>';
                $stmt->close();                                                                                                                 
            }
        } else {
            echo '<p>No positions available.</p>';
        }
        ?>
        
        <input type="hidden" name="candidate_id" id="candidate_id" value="">

        <div class="button-div">
            <button type="button" class="button-37" id="startCamera">Open Camera</button>
            <button type="submit" class="button-37" id="submitVoteBtn" hidden>Submit Vote</button>

            <div id="cameraModal" class="modal">
                <div class="modal-content">
                    <span id="closeCameraModal" class="close">&times;</span>
                    <video id="video-feed" autoplay></video>
                    <canvas id="canvas" ></canvas>
                </div>
            </div>

        
        </div>

    </form>
</div>

<?php
// Close the database connection
$conn->close();
?>
<script defer src="../backup-js/script.js"></script>



</body>
</html>
