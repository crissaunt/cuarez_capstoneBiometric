<?php
session_start();
include '../db/db_connection.php'; 
if (!isset($_SESSION['registeredNumber'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['registeredNumber'];

// Fetch user 
$userDetailsSql = "SELECT * FROM user_voter WHERE id = ?";
$stmt = $conn->prepare($userDetailsSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userDetailsResult = $stmt->get_result();
$userDetails = $userDetailsResult->fetch_assoc();
$stmt->close();

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
    <link rel="stylesheet" type="text/css" href="../CSS/style.css">
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
            <a href="logout.php"><button class="button-37">Logout</button></a>
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
            
            <div class="id">
          
             
              <input type="hidden" id="userRegistrationNumber" name="registerNumber" value="<?php echo $userDetails['registrationNumber']; ?>"   placeholder="Enter Your ID Number">
         
          </div>


            

        <?php
        if ($positionsResult->num_rows > 0) {
            while ($positionRow = $positionsResult->fetch_assoc()) {
                $positionId = htmlspecialchars($positionRow['id']);
                $positionName = htmlspecialchars($positionRow['position_name']);
                $positionmaxVote = htmlspecialchars($positionRow['max_vote']);

                echo '<div class="name-div">';
                echo '<div class="name"> ' . strtoupper($positionName) . '<span class="required">*</span>

                <div id="error-message"  style="color: red; display: none;">asdasdsadsa </div>
              
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
                        
                        $input = ($positionmaxVote > 1) 
                        ? '<input type="checkbox" class="' . $positionName . '" name="position_' . $positionId . '[]" value="' . $candidateId . '" >' 
                        : '<input type="radio" class="' . $positionName . '" name="position_' . $positionId . '" value="' . $candidateId . '" required>';
                    
                        json_encode($positionmaxVote);
                        json_encode($positionName);
                        echo '<div class="input-div">';
                    
                        echo $input;

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
<script defer src="../js/script.js"></script>

<script>

    const positionmaxVotes = <?php echo json_encode($positionmaxVote); ?>;
    const positionNames = <?php echo json_encode([$positionName]); ?>;

</script>



</body>
</html>
