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
    <script defer src="../js/script.js"></script>
    <link rel="stylesheet" type="text/css" href="../CSS/loader.css">
    <script src="../JS/loader.js"></script>

</head>


<div class="loader"></div>

<body data-registered-number="<?php echo htmlspecialchars($userDetails['registrationNumber']); ?>">



<header class="header">
    <div class="flex">
        <a href="#" class="logo"><img src="../img/image.png" alt="" width="200px"></a>
        <nav class="navbar">
            <!-- Nav -->
        </nav>
        <div class="icons">
            <!-- <a href="#"><button class="button-37">View Your Vote</button></a> -->
            <a href="logout.php"><button class="button-37">Logout</button></a>
        </div>
    </div>
</header>

<div class="form" >
   
        <!-- <div class="title-div">
            <img src="../img/image.png" alt="">
            <p class="required">*Click the name to view their profile</p>
            <p class="description">This is description...</p>
        </div> -->
            <div class="title-div" style="width: 200px">
                <!-- <h5 class="electionTitle">CEIT ELECTION 2023</h5> -->
                <img src="../img/elect.png" alt="" class="electionTitle"  width="100px">
            </div>


            <form action="vote.php" method="post" id="voteForm">
            
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
                <div id="error_' . $positionId . '" style="color: red; font-size: 14px;"></div>
            </div>';

        echo '<div class="maxVote">';
        if ($positionmaxVote > 1) {
            echo '<p>Select only ' . $positionmaxVote . ' candidate(s)</p>';
        } else {
            echo '<p>Select only ' . $positionmaxVote . ' candidate</p>';
        }
        echo '<button class="reset">Reset</button></div>';

        // JavaScript for limiting checkbox selection
        echo '<script>
            let maxVotes_' . $positionId . ' = ' . $positionmaxVote . ';
            document.addEventListener("DOMContentLoaded", function() {
                let checkboxes = document.querySelectorAll(".' . $positionName . '");
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener("change", function() {
                        let checkedCount = document.querySelectorAll(".' . $positionName . ':checked").length;
                        let errorElement = document.getElementById("error_' . $positionId . '");
                        if (checkedCount > maxVotes_' . $positionId . ') {
                            checkbox.checked = false; // Uncheck the box
                            errorElement.innerText = "You can select only " + maxVotes_' . $positionId . ' + " candidates.";
                        } else {
                            errorElement.innerText = ""; // Clear the error message
                        }
                    });
                });
            });
        </script>';

        // Fetch candidates for the current position
        $candidatesSql = "SELECT id, first_name, last_name, program, year_level, image,platform FROM candidates WHERE position_id = ?";
        $stmt = $conn->prepare($candidatesSql);
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $candidatesResult = $stmt->get_result();

        if ($candidatesResult->num_rows > 0) {
            while ($candidateRow = $candidatesResult->fetch_assoc()) {
                $candidateId = htmlspecialchars($candidateRow['id']);
                $candidateName = htmlspecialchars($candidateRow['first_name'] . ' ' . $candidateRow['last_name']);
                $candidateProgram = htmlspecialchars($candidateRow['program']);
                $candidateYearLevel = htmlspecialchars($candidateRow['year_level']);
                $imagePath = "{$candidateRow['first_name']}_{$candidateRow['last_name']}/{$candidateRow['image']}";
                $candidatePlatform = htmlspecialchars($candidateRow['platform']);


                $input = ($positionmaxVote > 1)
                    ? '<input type="checkbox" class="' . htmlspecialchars($positionName) . '" name="position_' . htmlspecialchars($positionId) . '[]" value="' . htmlspecialchars($candidateId) . '">'
                    : '<input type="radio" class="' . htmlspecialchars($positionName) . '" name="position_' . htmlspecialchars($positionId) . '" value="' . htmlspecialchars($candidateId) . '" >';

                echo '<div class="input-div">';
                echo $input;
                echo '<img src="../img/' . $imagePath . '" alt="Candidate Image" width="125" height="125">';
                echo '<div class="candidateInfo">';
                echo '<label>' . $candidateName . '</label>';
                echo '<p>' . $candidateProgram . ' ' . $candidateYearLevel . '</p>';
                // echo '<button>See more...</button>';
                echo '<span class="see-more" data-name="' . $candidateName . '" data-program="' . $candidateProgram . '" data-year="' . $candidateYearLevel . '" data-platform="' . $candidatePlatform . '" data-image="' . $imagePath . '">See more...</span>';
                echo '</div></div>';
            }
        } else {
            echo '<p>No candidates available.</p>';
        }

        echo '</div>';
        $stmt->close();
    }
} else {
    echo '<p>No positions available.</p>';
}
?>


<!-- Modal Structure -->
<!-- <div id="candidateModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <img id="modalImage" src="" alt="Candidate Image" width="150" height="150">
        <h2 id="modalName"></h2>
        <p><strong>Program:</strong> <span id="modalProgram"></span></p>
        <p><strong>Year Level:</strong> <span id="modalYear"></span></p>
        <p><strong>Platform:</strong> <span id="modalPlatform"></span></p>
    </div>
</div> -->

<script>
// JavaScript for modal functionality
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("candidateModal");
    const closeButton = document.querySelector(".close-button");
    
    document.querySelectorAll(".see-more").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("modalImage").src = "../img/" + this.dataset.image;
            document.getElementById("modalName").innerText = this.dataset.name;
            document.getElementById("modalProgram").innerText = this.dataset.program;
            document.getElementById("modalYear").innerText = this.dataset.year;
            document.getElementById("modalPlatform").innerText = this.dataset.platform;
            
            modal.style.display = "block";
        });
    });
    
    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });
    
    window.addEventListener("click", function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});
</script>

<?php
if ($positionsResult->num_rows > 0) {
    while ($positionRow = $positionsResult->fetch_assoc()) {
        $positionId = htmlspecialchars($positionRow['id']);
        $positionName = htmlspecialchars($positionRow['position_name']);
        $positionmaxVote = htmlspecialchars($positionRow['max_vote']);

        echo '<div class="name-div">';
        echo '<div class="name"> ' . strtoupper($positionName) . '<span class="required">*</span>
                <div id="error_' . $positionId . '" style="color: red; font-size: 14px;"></div>
            </div>';

        echo '<div class="maxVote">';
        if ($positionmaxVote > 1) {
            echo '<p>Select only ' . $positionmaxVote . ' candidate(s)</p>';
        } else {
            echo '<p>Select only ' . $positionmaxVote . ' candidate</p>';
        }
        echo '<button class="reset">Reset</button></div>';

        // Fetch candidates for the current position
        $candidatesSql = "SELECT id, first_name, last_name, program, year_level, platform, image FROM candidates WHERE position_id = ?";
        $stmt = $conn->prepare($candidatesSql);
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $candidatesResult = $stmt->get_result();

        if ($candidatesResult->num_rows > 0) {
            while ($candidateRow = $candidatesResult->fetch_assoc()) {
                $candidateId = htmlspecialchars($candidateRow['id']);
                $candidateName = htmlspecialchars($candidateRow['first_name'] . ' ' . $candidateRow['last_name']);
                $candidateProgram = htmlspecialchars($candidateRow['program']);
                $candidateYearLevel = htmlspecialchars($candidateRow['year_level']);
                $candidatePlatform = htmlspecialchars($candidateRow['platform']);
                $imagePath = htmlspecialchars($candidateRow['image']);

                $input = ($positionmaxVote > 1)
                    ? '<input type="checkbox" class="' . htmlspecialchars($positionName) . '" name="position_' . htmlspecialchars($positionId) . '[]" value="' . htmlspecialchars($candidateId) . '">'
                    : '<input type="radio" class="' . htmlspecialchars($positionName) . '" name="position_' . htmlspecialchars($positionId) . '" value="' . htmlspecialchars($candidateId) . '" >';

                echo '<div class="input-div">';
                echo $input;
                echo '<img src="../img/' . $imagePath . '" alt="Candidate Image" width="125" height="125">';
                echo '<div class="candidateInfo">';
                echo '<label>' . $candidateName . '</label>';
                echo '<p>' . $candidateProgram . ' ' . $candidateYearLevel . '</p>';
                echo '</div></div>';
                echo '<button class="see-more" data-name="' . $candidateName . '" data-program="' . $candidateProgram . '" data-year="' . $candidateYearLevel . '" data-platform="' . $candidatePlatform . '" data-image="' . $imagePath . '">See more...</button>';

            }
        } else {
            echo '<p>No candidates available.</p>';
        }

        echo '</div>';
        $stmt->close();
    }
} else {
    echo '<p>No positions available.</p>';
}
?>
<!-- Modal Structure -->
<div id="candidateModal" class="candidateModal" style="display:none;">
    <div class="modal-candidateModal">
        <span class="close-button">&times;</span>
        <img id="modalImage" src="" alt="Candidate Image" width="150" height="150">
        <h2 id="modalName"></h2>
        <p><strong>Program:</strong> <span id="modalProgram"></span></p>
        <p><strong>Year Level:</strong> <span id="modalYear"></span></p>
        <p><strong>Platform:</strong> <span id="modalPlatform"></span></p>
    </div>
</div>

<script>
// JavaScript for modal functionality
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("candidateModal");
    const closeButton = document.querySelector(".close-button");
    
    document.querySelectorAll(".see-more").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("modalImage").src = "../img/" + this.dataset.image;
            document.getElementById("modalName").innerText = this.dataset.name;
            document.getElementById("modalProgram").innerText = this.dataset.program;
            document.getElementById("modalYear").innerText = this.dataset.year;
            document.getElementById("modalPlatform").innerText = this.dataset.platform;
            
            modal.style.display = "block";
        });
    });
    
    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });
    
    window.addEventListener("click", function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});


</script>

<!-- CSS for Modal -->
<style>
.candidateModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}
.see-more {
    cursor: pointer;
    background-color: #093704;
    padding: 8px;
    color: white;
    border-radius: 5px;
}
.see-more:hover {
    background-color: green;
}

.modal-candidateModal {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 400px;
    text-align: center; 
    border-radius: 8px;
    position: relative;
}

.close-button {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close-button:hover,
.close-button:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>

    
        
        <input type="hidden" name="candidate_id" id="candidate_id" value="">

        <div class="button-div">
            <button type="button" class="button-37" id="startCamera">Open Camera</button>
            <button type="submit" class="button-37" id="submitVoteBtn" hidden>Submit Vote</button>

            <div id="cameraModal" class="modal">
                <div class="modal-content">
                    <span id="closeCameraModal" class="close">&times;</span>
                    <video id="video-feed" autoplay></video>
                    <canvas id="canvas" ></canvas>
                    <div id="error-message"></div>

                </div>
            </div>

        
        </div>

    </form>
</div>

<?php
// Close the database connection
$conn->close();
?>



</body>
</html>
