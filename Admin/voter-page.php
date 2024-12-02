<?php
    include '../db/db_connection.php';
    include 'layout.php';

   


   

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    $records_per_page = 10;

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $records_per_page;

    // Fetch voter data from the database with LIMIT and OFFSET
    $sql = "SELECT * FROM user_voter LIMIT $records_per_page OFFSET $offset";
    $result = $conn->query($sql);

    $voters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $voters[] = $row;  // Store each row in the array
           
        }
    } else {
        echo "0 results";
    }

    // Fetch the total number of voters
    $total_voters_sql = "SELECT COUNT(*) AS total FROM user_voter";
    $total_result = $conn->query($total_voters_sql);
    $total_voters = $total_result->fetch_assoc()['total'];

    $conn->close();

    // Calculate the total pages
    $total_pages = ceil($total_voters / $records_per_page);
?>

<section class="home-section">
    <div class="text">
        <div class="dashboard">
            <h1>Student Voter List</h1>

            <!-- ADD MODAL -->
            <div class="add-button">
                <button id="openModal">Add Voter</button>
            </div>

            <div class="container">
                <div class="dropdown-entry">
                <form action="" method="GET">
                    <label for="show">Show</label>
                    <select name="entries" id="show" onchange="this.form.submit()">
                        <option value="10" <?= isset($_GET['entries']) && $_GET['entries'] == 10 ? 'selected' : '' ?>>10</option>
                        <option value="30" <?= isset($_GET['entries']) && $_GET['entries'] == 30 ? 'selected' : '' ?>>30</option>
                        <option value="50" <?= isset($_GET['entries']) && $_GET['entries'] == 40 ? 'selected' : '' ?>>40</option>
                        <option value="100" <?= isset($_GET['entries']) && $_GET['entries'] == 70 ? 'selected' : '' ?>>70</option>
                    </select>
                    <label for="show">Entries</label>
                </form>
                </div>
                <div class="search">
                    <input type="text" placeholder="Search..." />
                    <button type="submit">Search</button>
                </div>
            </div>
            <script src="JS/students.js"></script>
            <div id="addPositionModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span> <!-- Close button -->
                    <form action="voter/add.php" method="POST" onsubmit="return validateForm()">
                        <!-- Corrected CSRF token -->
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['user']; ?>">
                        <div class="input-row">
                            <div class="input-box">
                                <label for="id">ID Number:</label>
                                <input type="text" name="registrationNumber" id="registrationNumber">     
                            </div>
                            <div class="input-box">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name" id="first_name">     
                            </div>
                            <div class="input-box">
                                <label for="last_name">Last Name:</label>
                                <input type="text" name="last_name" id="last_name">  
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="gender">Gender:</label>
                                <select name="gender" id="gender">
                                    <option hidden>Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                              
                            </div>
                            <div class="input-box">
                                <label for="program">Program:</label>
                                <input type="text" name="program" id="program">
                            </div>    
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="email">Email:</label>
                                <input type="text" name="email" id="email">
                            </div>
                            <div class="input-box">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password">
                            </div>    
                        </div>     
                        <div class="input-row">
                                <div class="input-box">
                                    <button type="button" id="capture-btn1" onclick="openCamera('capture-btn1')">Open Camera 1</button>
                                    <video id="capture-btn1-video" autoplay muted style="display: none; width: 100%; border: 1px solid #ccc;"></video>
                                    <img id="capture-btn1-captured-image" style="display: none; width: 100%; border: 1px solid #ccc;" alt="Loading..." />
                                    <!-- Add this inside your HTML, perhaps near the video element -->
                                    <div id="loading-indicator" style="display: none;font-size:x-small;">Processing, please wait...</div>
                                    <input type="hidden" id="capture-btn1-captured-image-input" name="studentimage1" />
                                </div>
                                <div class="input-box">
                                    <button type="button" id="capture-btn2" onclick="openCamera('capture-btn2')">Open Camera 2</button>
                                    <video id="capture-btn2-video" autoplay muted style="display: none; width: 100%; border: 1px solid #ccc;"></video>
                                    <img id="capture-btn2-captured-image" style="display: none; width: 100%; border: 1px solid #ccc;" alt="Loading..." />
                                    <!-- Add this inside your HTML, perhaps near the video element -->
                                    <div id="loading-indicator" style="display: none;">Processing, please wait...</div>
                                    <input type="hidden" id="capture-btn2-captured-image-input" name="studentimage2" />
                                </div>
                            </div>
<!-- 
                        <script src="JS/addstudent.js"></script> -->
                        <button type="submit" name="add_voter" >Submit</button>
                    </form>
                </div>
            </div>


            <style>
                /* Add your styles here */
                .video-container {
                    display: none; /* Initially hidden */
                    position: relative;
                }
                video {
                    width: 100%;
                    height: auto;
                }
            </style>

            <!-- Edit modal -->
          
            <div id="editVoterModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span> <!-- Close button -->
                    <form action="voter/edit.php" method="POST">
                        <h2>Edit Voter</h2>
                        <input type="hidden" name="id" id="editVoterId">
                        <div class="input-row">
                            <div class="input-box">
                                <label for="first_name">First Name:</label>
                                <input type="text" id="editFirstName" name="first_name" required>
                            </div>
                            <div class="input-box">
                                <label for="last_name">Last Name:</label>
                                <input type="text" id="editLastName" name="last_name" required>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="gender">Gender:</label>
                                <input type="text" id="editGender" name="gender" required>
                            </div>
                            <div class="input-box">
                                <label for="program">Program:</label>
                                <input type="text" id="editProgram" name="program" required>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="email">Email:</label>
                                <input type="email" id="editEmail" name="email" required>
                            </div>
                            <div class="input-box">
                                <label for="password">Password:</label>
                                <input type="text" id="editPassword" name="password" required>
                            </div>
                        </div>
                        <button type="submit" name="update_voter">Submit</button>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <th>Voters ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Program</th>
                 
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php foreach ($voters as $voter) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['registrationNumber']); ?></td>
                            <td><?php echo htmlspecialchars($voter['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['gender']); ?></td>
                            <td>
                            <?php
                                $platformText = htmlspecialchars($voter['email']); 
                                $shortenedText = (strlen($platformText) > 9) ? substr($platformText, 0, 9) . '...' : $platformText;
                                echo $shortenedText;
                            ?>
                            </td>
                            <td><?php echo htmlspecialchars($voter['program']); ?></td>
                            
                            <td>
                                <button class="edit-button" onclick='openEditModal(<?php echo json_encode($voter); ?>)'>Edit</button>
                                <button class="delete-button" onclick="confirmDelete(<?php echo htmlspecialchars($voter['id']); ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pagination">
                <p>Page: <?php echo $page; ?></p>
                <div class="page">
                    <?php if ($page > 1) { ?>
                        <button onclick="window.location.href='?page=<?php echo $page - 1; ?>'">
                            <i class='bx bx-left-arrow'></i> Previous
                        </button>
                    <?php } ?>
                    
                    <?php if ($page < $total_pages) { ?>
                        <button onclick="window.location.href='?page=<?php echo $page + 1; ?>'">
                            Next <i class='bx bx-right-arrow'></i>
                        </button>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Include the face-api.js library -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

<!-- Include the face-api.js library -->
<script src="../face-api.min.js"></script> 
<script>
async function openCamera(buttonId) {
  try {
    // Access the webcam
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const videoElement = document.getElementById(buttonId + '-video');
    
    videoElement.style.display = 'block'; // Make video visible
    videoElement.srcObject = stream;

    // Create a capture button dynamically
    const captureButton = document.createElement('button');
    captureButton.textContent = 'Capture Image';
    captureButton.style.marginTop = '10px';
    videoElement.parentNode.appendChild(captureButton);

    captureButton.onclick = async () => {
      const capturedImageDataUrl = captureImage(videoElement);

      // Stop the video stream
      stream.getTracks().forEach(track => track.stop());
      videoElement.style.display = 'none';
      videoElement.parentNode.removeChild(captureButton);

      // Display the captured image
      const imgElement = document.getElementById(buttonId + '-captured-image');
      imgElement.src = capturedImageDataUrl;
      imgElement.style.display = 'block';

      // Perform face detection on the captured image
      await detectFace(capturedImageDataUrl, buttonId);

      // Update the hidden input with the captured image data
      const hiddenInput = document.getElementById(buttonId + '-captured-image-input');
      hiddenInput.value = capturedImageDataUrl;
    };
  } catch (error) {
    console.error('Error accessing webcam:', error);
    alert('Unable to access the webcam. Please check your permissions.');
  }
}

document.querySelectorAll('.close').forEach(closeBtn => {
  closeBtn.addEventListener('click', () => {
    const videoElements = document.querySelectorAll('video');
    videoElements.forEach(videoElement => stopCamera(videoElement));
  });
});

function stopCamera(videoElement) {
  const stream = videoElement.srcObject;
  if (stream) {
    stream.getTracks().forEach(track => track.stop());
    videoElement.srcObject = null;
  }
  videoElement.style.display = 'none';
  const captureButton = videoElement.parentNode.querySelector('button');
  if (captureButton) {
    captureButton.remove();
  }
}


function captureImage(video) {
  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const context = canvas.getContext('2d');
  context.drawImage(video, 0, 0, canvas.width, canvas.height);
  return canvas.toDataURL('image/png');
}

// Load face-api.js models
async function loadModels() {
  const MODEL_URL = '/models'; // Ensure models are placed in this folder
  await faceapi.loadSsdMobilenetv1Model(MODEL_URL);
  await faceapi.loadFaceLandmarkModel(MODEL_URL);
  await faceapi.loadFaceRecognitionModel(MODEL_URL);
}

// Detect a face from the captured image
// Detect a face from the captured image
async function detectFace(imageDataUrl, buttonId) {
  const img = new Image();
  img.src = imageDataUrl;

  // Show the loading indicator
  const loadingIndicator = document.getElementById('loading-indicator');
  loadingIndicator.style.display = 'block';

  img.onload = async () => {
    try {
      const canvas = document.createElement('canvas');
      canvas.width = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0);

      const detections = await faceapi
        .detectSingleFace(canvas)
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (detections) {
        console.log('Face detected:', detections);
        alert('Face detected!');
      } else {
        console.log('No face detected.');
        alert('No face detected. Please try again.');
        const imgElement = document.getElementById(buttonId + '-captured-image');
                 imgElement.style.display = 'none';  // Hide the captured image

                openCamera(buttonId);  // Restart the camera
                return;
      }
    } catch (error) {
      console.error('Error during face detection:', error);
      alert('An error occurred during face detection.');
    } finally {
      // Hide the loading indicator after processing completes
      loadingIndicator.style.display = 'none';
    }
  };



}




// Call this to ensure models are loaded when the page is ready
window.onload = loadModels;
</script>
