<?php include 'layout.php'; ?>
<?php

$addsuccess = '';
if (isset($_GET['addsuccess'])){
$addsuccess = 'Position Added Successfully'; 
}

if (isset($_GET['updatesuccess'])){
    $addsuccess = 'Position Update Successfully'; 
}


if (isset($_GET['deletesuccess'])){
    $addsuccess = 'Position DELETE Successfully'; 
}



?>



  <section class="home-section">
    <div class="text">
        <div class="dashboard">
            <h1>Positions List</h1>
           <!-- Modal Trigger Button -->
            <div class="add-button">
                <button id="openModal">Add Position</button>
            </div>
            <div class="message">
                <h3><?php echo $addsuccess; ?></h3>
            </div>
            <!-- Modal Structure -->
            <div id="addPositionModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span> <!-- Close button added here -->
                    <form action="position/add.php" method="POST">
                        <h2>Add Position</h2>
                        <div class="input-row">
                            <div class="input-box">
                                <label for="position">Position:</label>
                                <input type="text" name="position">
                            </div>
                            <div class="input-box">
                                <label for="max_votes">Maximum Votes:</label>
                                <input type="number" name="max_votes" value=1>
                            </div>
                            <div class="input-box">
                                <label for="priority">Priority:</label>
                                <input type="text" name="priority" placeholder="Edit Priority according to your like" readonly >
                            </div>
                        </div>
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>

            <div class="container">
                <div class="dropdown-entry">
                    <form action="">
                        <label for="show">Show</label>
                        <select name="entries" id="show">
                            <option value="10">10</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="70">70</option>
                        </select>
                        <label for="show">Entries</label>
                    </form>
                </div>
                <div class="search">
                    <input type="text" placeholder="Search..." />
                    <button type="submit">Search</button>
                </div>
            </div>




         <!-- Edit modal -->

        <!-- Modal Structure -->
                <div id="editPositionModal" class="modal">
                    <div class="modal-content" style="margin-left: 300px;">
                        <span class="close">&times;</span> <!-- Close button -->
                        <form action="position/edit.php" method="POST">
                            <h2>Edit Position</h2>
                            <div class="input-row">
                                <!-- Hidden ID Field -->
                                <input type="hidden" id="position_id" name="id">
                                
                                <!-- Position Name -->
                                <div class="input-box">
                                    <label for="position">Position:</label>
                                    <input type="text" id="position" name="position" required>
                                </div>
                                
                                <!-- Maximum Votes -->
                                <div class="input-box">
                                    <label for="max_votes">Maximum Votes:</label>
                                    <input type="number" id="max_votes" name="max_vote" min="1" required>
                                </div>
                                
                                <!-- Priority -->
                                <div class="input-box">
                                    <label for="priority">Priority:</label>
                                    <input type="number" id="priority" name="priority" placeholder="Edit Priority" >
                                </div>
                            </div>
                            <button type="submit" name="update_position">Submit</button>
                        </form>
                    </div>
                </div>















            <table>
            <?php
            include '../db/db_connection.php';
            $sql = "SELECT * FROM positions ORDER BY priority ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()){ 

                    $positions[]= [
                    'id' => $row['id'],
                    'position_name' => $row['position_name'],
                    'max_vote' => $row['max_vote'],
                    'priority'=> $row['priority'],];
                }

            }

            

             ?>

                <thead>
                        <th>No.<i class='bx bx-sort-down'></i></th>
                        <th>Position <i class='bx bx-sort-up'></i></th>
                        <th>Max Votes <i class='bx bx-sort-up'></i></th>
                        <th>Priority <i class='bx bx-sort-up'></i></th>
                        <th>action</th>
                        
                </thead>
                <tbody>
                    <tr>
                        <?php foreach ($positions as $position) { ?>
                            <td><?php echo htmlspecialchars($position['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($position['position_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($position['max_vote'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($position['priority'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><button class="edit-button" onclick='openEditModal(<?php echo json_encode($position); ?>)'>Edit</button>                          
                            <button class="delete-button" onclick="deletePosition(<?php echo htmlspecialchars($position['id']); ?>)">Delete</button>
                            
                            </td>
                 


                    </tr>
                         <?php } ?>
            </tbody>
            </table>
            <div class="pagination">
                <div class="value">
                    <p> Page: 5</p>
                </div>
                <div class="page">
                    <button><i class='bx bx-left-arrow'></i> Previous</button>
                    <button> Next <i class='bx bx-right-arrow'></i></button>
                </div> 
            </div>
        </div>
    </div>
  </section>

  <script>
    // Open Modal with Pre-Filled Data
function openEditModal(position) {
    // Get modal elements
    const modal = document.getElementById("editPositionModal");
    const positionIdField = document.getElementById("position_id");
    const positionField = document.getElementById("position");
    const maxVotesField = document.getElementById("max_votes");
    const priorityField = document.getElementById("priority");

    // Set modal field values from the passed `position` object
    positionIdField.value = position.id;
    positionField.value = position.position_name;
    maxVotesField.value = position.max_vote;
    priorityField.value = position.priority;

    // Display the modal
    modal.style.display = "block";
}

// Close Modal
document.querySelector(".close").addEventListener("click", () => {
    document.getElementById("editPositionModal").style.display = "none";
});

// Close Modal on Outside Click
window.addEventListener("click", (event) => {
    const modal = document.getElementById("editPositionModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

  </script>

</body>
</html>