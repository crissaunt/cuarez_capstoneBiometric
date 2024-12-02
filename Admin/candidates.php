<?php include 'layout.php'; ?>
<?php

$addsuccess = '';
if (isset($_GET['addsuccess'])){
$addsuccess = 'Position Added Successfully'; 
}

if (isset($_GET['updatesuccess'])){
    $addsuccess = 'Candidate Update Successfully'; 
}


if (isset($_GET['deletesuccess'])){
    $addsuccess = 'Position DELETE Successfully'; 
}



?>

<script>
    function resetCandidates() {
        if (confirm('Are you sure you want to reset all candidates?')) {
            window.location.href = 'reset_candidates.php'; // Assuming you have a reset_candidates.php that handles resetting the candidates.
        }
    }
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Your form validation logic here
    document.getElementById("addForm").addEventListener("submit", function(event) {
        let valid = true;

        // Clear previous error messages
        const errorElements = document.querySelectorAll('.error');
        errorElements.forEach((el) => el.innerHTML = "");

        // Get form fields
        const firstName = document.getElementById("addfirst_name");
        const lastName = document.getElementById("addlast_name");
        const platform = document.getElementById("addplatform");
        const program = document.getElementById("program");
        const yearLevel = document.getElementById("addyear_level");
        const position = document.getElementById("position_id");
        const image = document.getElementById("addimage");

        // Validate First Name
        if (firstName.value.trim() === "") {
            valid = false;
            firstName.nextElementSibling.innerHTML = "First Name is required.";
        }

        // Validate Last Name
        if (lastName.value.trim() === "") {
            valid = false;
            lastName.nextElementSibling.innerHTML = "Last Name is required.";
        }

        // Validate Platform
        if (platform.value.trim() === "") {
            valid = false;
            platform.nextElementSibling.innerHTML = "Platform is required.";
        }

        // Validate Program
        if (program.value.trim() === "") {
            valid = false;
            program.nextElementSibling.innerHTML = "Program is required.";
        }

        // Validate Year Level
        if (yearLevel.value.trim() === "") {
            valid = false;
            yearLevel.nextElementSibling.innerHTML = "Year Level is required.";
        }

         // Validate Position (Select element)
         if (position.value === "" || position.selectedIndex === 0) {
            valid = false;
            position.nextElementSibling.innerHTML = "Please select a position.";
        }

        // Validate Image upload
        if (image.files.length === 0) {
            valid = false;
            image.nextElementSibling.innerHTML = "Please upload an image.";
        }

        // If the form is invalid, prevent submission
        if (!valid) {
            event.preventDefault();
        }
    });
  });
</script>


<style>
    .error {
        color: red;
        font-size: 12px;
    }
</style>








  <section class="home-section">
    <div class="text">
        <div class="dashboard">
            <a href="candidates.php" style="text-decoration: none; color:#1f3b23;" ><h1>Candidate List</h1></a>
             <!-- Modal Trigger Button -->
             <div class="message">
                <h3><?php echo $addsuccess; ?></h3>
            </div>
             <div class="add-button">
                <button id="openModal">Add Candidate</button>
                <div class="reset-button">
                    <button onclick="resetCandidates()">Reset Candidates</button>
                </div>
            </div>
            <!-- Modal Structure -->
            <div id="addPositionModal" class="modal" >
                <div class="modal-content">
                    <span class="close">&times;</span> <!-- Close button added here -->
                    <form action="candidate/add.php" method="POST" enctype="multipart/form-data" id="addForm">
                    <h2>Add Candidate</h2>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name" id="addfirst_name">  
                                <div class="error"></div>   
                            </div>
                            <div class="input-box">
                                <label for="last_name">Last Name:</label>
                                <input type="text" name="last_name" id="addlast_name">  
                                <div class="error"></div>   
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="platform">Platform:</label>
                                <input type="text" name="platform" id="addplatform"> 
                                <div class="error"></div>   
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-box">
                                <label for="program">Program:</label>
                                <input type="text" name="program" id="program">
                                <div class="error"></div>   
                            </div>    
                            <div class="input-box">
                                <label for="year_level">Year Level:</label>
                                <input type="text" name="year_level" id="addyear_level">
                                <div class="error"></div>   
                            </div>  
                          
                        
                            <?php
                            include '../db/db_connection.php';

                            $positions = "SELECT id, position_name FROM positions";
                            $positionsResult = $conn->query($positions);

                            if (!$positionsResult) {
                                die("Error: " . $conn->error);
                            }

                            $selectpositions = []; // Initialize the array to store positions
                            if ($positionsResult->num_rows > 0) {
                                while($row = $positionsResult->fetch_assoc()){ 
                                    $selectpositions[] = [
                                        'id' => $row['id'],
                                        'position_name' => $row['position_name'],
                                    ];
                                }
                            }
                            ?>

                            <div class="input-box">
                                <label for="position_id">Position:</label>
                                <select name="position_id" id="position_id">
                                    <?php foreach ($selectpositions as $position) { ?>
                                        <option hidden>Select Position</option>
                                        <option value="<?php echo htmlspecialchars($position['id']); ?>">
                                            <?php echo htmlspecialchars($position['position_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="error"></div>   
                            </div>
                           

                        </div>   
                        <div class="input-row">
                                <div class="input-box">
                                    <label for="image">Upload Image:</label>
                                    <input type="file" name="image" id="addimage">
                                    <div class="error"></div>   
                                </div>
                        </div>     
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>


            <!-- Modal Structure -->
            <div id="editPositionModal" class="modal">
                    <div class="modal-content" style="margin-left: 300px;">
                        <span class="close">&times;</span> <!-- Close button -->
                        <form action="candidate/edit.php" method="POST">
                            <h2>Edit Candidate</h2>
                            <div class="input-row">
                            <input type="hidden" name="id" >
                            <div class="input-box">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name">     
                            </div>
                            <div class="input-box">
                                <label for="last_name">Last Name:</label>
                                <input type="text" name="last_name">  
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="platform">Platform:</label>
                                <input type="text" name="platform">
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-box">
                                <label for="program">Program:</label>
                                <input type="text" name="program">
                            </div>    
                            <div class="input-box">
                                <label for="year_level">Year Level:</label>
                                <input type="text" name="year_level">
                            </div>  
                            <div class="input-box">
                                <label for="image">Image:</label>
                                <input type="text" name="image"> <!-- Or a file input if handling uploads -->

                            </div>  
                            <?php
                            include '../db/db_connection.php';

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            

                            $positions = "SELECT id, position_name FROM positions";
                            $positionsResult = $conn->query($positions);

                            if (!$positionsResult) {
                                die("Error: " . $conn->error);
                            }

                            $selectpositions = []; // Initialize the array to store positions
                            if ($positionsResult->num_rows > 0) {
                                while($row = $positionsResult->fetch_assoc()){ 
                                    $selectpositions[] = [
                                        'id' => $row['id'],
                                        'position_name' => $row['position_name'],
                                    ];
                                }
                            }
                            ?>

                            <div class="input-box">
                                <label for="position_id">Position:</label>
                                <select name="position_id" id="position_id">
                                    <?php foreach ($selectpositions as $position) { ?>
                                        <option value="<?php echo htmlspecialchars($position['id']); ?>">
                                            <?php echo htmlspecialchars($position['position_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>


                            </div>



                        </div>   
                            <button type="submit" name="update_candidate">Submit</button>
                        </form>
                    </div>
                </div>


















              

                <table>
                <?php
                include '../db/db_connection.php';

                // Get search term, entries per page, and current page
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $records_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $records_per_page;

                // Build SQL query to search only by name (first_name and last_name)
                $sql = "SELECT c.id, c.first_name, c.last_name, c.platform, c.program, c.year_level, p.position_name, c.position_id ,c.image 
                        FROM candidates c 
                        JOIN positions p ON c.position_id = p.id 
                        WHERE c.first_name LIKE ? OR c.last_name LIKE ? 
                        LIMIT ? OFFSET ?";

                $stmt = $conn->prepare($sql);
                $searchTerm = "%" . $search . "%"; // Wildcard search
                $stmt->bind_param("ssii", $searchTerm, $searchTerm, $records_per_page, $offset);
                $stmt->execute();
                $result = $stmt->get_result();

                $candidates = []; 
                if ($result) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { 
                            $candidates[] = [
                                'id' => $row['id'],
                                'first_name' => $row['first_name'],
                                'last_name' => $row['last_name'],
                                'platform' => $row['platform'],
                                'program' => $row['program'],
                                'year_level' => $row['year_level'],
                                'position_name' => $row['position_name'],
                                'position_id' => $row['position_id'],
                                'image' => $row['image'],
                            ];
                        }
                    } else {
                        $noCandidates = "<td colspan='8' style='letter-spacing: 2px;'>No candidates found.</td>"; 
                    }
                } else {
                    echo "<p>Error retrieving candidates: " . $conn->error . "</p>";
                }

                // Get total number of candidates based on the search
                $total_candidates_sql = "SELECT COUNT(*) AS total FROM candidates c WHERE c.first_name LIKE ? OR c.last_name LIKE ?";
                $total_stmt = $conn->prepare($total_candidates_sql);
                $total_stmt->bind_param("ss", $searchTerm, $searchTerm);
                $total_stmt->execute();
                $total_result = $total_stmt->get_result();
                $total_candidates = $total_result->fetch_assoc()['total'];

                $conn->close();

                // Calculate total pages
                $total_pages = ceil($total_candidates / $records_per_page);
                ?>

                    <div class="container">
                        <div class="dropdown-entry">
                            <form action="" method="GET">
                                <label for="show">Show</label>
                                <select name="entries" id="show" onchange="this.form.submit()">
                                    <option value="10" <?= $records_per_page == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="30" <?= $records_per_page == 30 ? 'selected' : '' ?>>30</option>
                                    <option value="40" <?= $records_per_page == 40 ? 'selected' : '' ?>>40</option>
                                    <option value="70" <?= $records_per_page == 70 ? 'selected' : '' ?>>70</option>
                                </select>
                                <label for="show">Entries</label>
                            </form>
                        </div>
                        <div class="search">
                            <form action="" method="GET">
                                <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" />
                                <input type="hidden" name="entries" value="<?= $records_per_page ?>" />
                                <button type="submit">Search</button>
                            </form>
                        </div>
                    </div>





                    <thead> 
                            <th>No. <i class='bx bx-sort-up'></i></th>
                            <th>Position <i class='bx bx-sort-down'></i></th>
                            <th>Name <i class='bx bx-sort-up'></i></th>
                        
                            <th>Platform <i class='bx bx-sort-down'></i></th>
                            <th>Program <i class='bx bx-sort-down'></i></th>
                            <th>Year Level <i class='bx bx-sort-down'></i></th>
                            <th>Image <i class='bx bx-sort-down'></i></th>

                            <th>Action <i class='bx bx-sort-down'></i></th>
                    </thead>
                    <tbody>
                        <tr>
                        <?php if (!empty($candidates)) { ?>
                        <?php foreach ($candidates as $candidate) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidate['id']); ?></td>
                                <td><?php echo  htmlspecialchars($candidate['position_name']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['first_name']); ?>
                                <?php echo htmlspecialchars($candidate['last_name']); ?></td>
                                <td>
                                    <?php 
                                        $platformText = htmlspecialchars($candidate['platform']); 
                                        $shortenedText = (strlen($platformText) > 15) ? substr($platformText, 0, 15) . '...' : $platformText;
                                        echo $shortenedText;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidate['program']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['year_level']); ?></td>
                                <td>
                                <?php 
                                $imagePath = "{$candidate['first_name']}_{$candidate['last_name']}/{$candidate['image']}";
                                echo "<img src='../img/$imagePath' alt='Candidate Image' width='60'>";
                                ?>

                                </td>
                                <td>
                                    <button class="edit-button" onclick='openEditModal(<?php echo json_encode($candidate); ?>)'>Edit</button> 
                                    <button class="delete-button" onclick="deleteCandidate(<?php echo htmlspecialchars($candidate['id']); ?>)">Delete</button>
                                </td>   
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="8">No candidates found.</td></tr>
                    <?php } ?>

        
                    </tbody>
                </table>
                <div class="pagination">
                    <div class="value">
                        <p> Page: <?= $page ?> of <?= $total_pages ?></p>
                    </div>
                    <div class="page">
                        <!-- Previous Button -->
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&entries=<?= $records_per_page ?>">
                                <button><i class='bx bx-left-arrow'></i> Previous</button>
                            </a>
                        <?php else: ?>
                            <button disabled><i class='bx bx-left-arrow'></i> Previous</button>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&entries=<?= $records_per_page ?>">
                                <button> Next <i class='bx bx-right-arrow'></i></button>
                            </a>
                        <?php else: ?>
                            <button disabled> Next <i class='bx bx-right-arrow'></i></button>
                        <?php endif; ?>
                    </div> 
                </div>

            </div>
        </div>
    </section>
<script>

// Open edit modal and populate fields with candidate data
function openEditModal(candidate) {
    const modal = document.getElementById("editPositionModal");
    if (!modal) {
        console.error("Modal not found!");
        return;
    }

    // Select and set each input safely
    const setFieldValue = (selector, value) => {
        const field = modal.querySelector(selector);
        if (field) field.value = value;
        else console.warn(`${selector} not found in the modal.`);
    };

    setFieldValue("input[name='id']", candidate.id);
    setFieldValue("input[name='first_name']", candidate.first_name);
    setFieldValue("input[name='last_name']", candidate.last_name);
    setFieldValue("textarea[name='platform']", candidate.platform);
    setFieldValue("input[name='program']", candidate.program);
    setFieldValue("input[name='year_level']", candidate.year_level);
    setFieldValue("input[name='image']", candidate.image);
    setFieldValue("select[name='position_id']", candidate.position_id);

    // Show the modal
    modal.style.display = "block";
}


// Close modal functionality
function closeModal() {
    document.getElementById("editPositionModal").style.display = "none";
}


// Close modal when 'x' is clicked
document.querySelectorAll(".close").forEach(closeButton => {
    closeButton.addEventListener("click", closeModal);
});

// Close modal if clicked outside
window.addEventListener("click", (event) => {
    if (event.target.classList.contains("modal")) {
        closeModal();
    }
});

    

</script>
</body>
</html>