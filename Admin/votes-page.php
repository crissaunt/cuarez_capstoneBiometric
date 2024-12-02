<?php include 'layout.php'; 

$message = '';
if (isset($_GET['success'])){

   $message = 'Votes reset successfully.';

}


?>


<section class="home-section">
    <div class="text">
        <div class="dashboard">
            <h1>Votes List</h1>
            <?php
            if($message){?>
            <div class="message">
                <h3><?php echo $message; ?></h3>
            </div>
                
        <?php    }?>
            

            <div class="reset-button">
                <button onclick="resetVotes()">Reset Votes</button>
            </div>

            <div class="container">
                <div class="dropdown-entry">
                    <form action="" method="GET">
                        <label for="show">Show</label>
                        <select name="entries" id="show" onchange="this.form.submit()">
                            <option value="10" <?= isset($_GET['entries']) && $_GET['entries'] == 10 ? 'selected' : '' ?>>10</option>
                            <option value="30" <?= isset($_GET['entries']) && $_GET['entries'] == 30 ? 'selected' : '' ?>>30</option>
                            <option value="40" <?= isset($_GET['entries']) && $_GET['entries'] == 40 ? 'selected' : '' ?>>40</option>
                            <option value="70" <?= isset($_GET['entries']) && $_GET['entries'] == 70 ? 'selected' : '' ?>>70</option>
                        </select>
                        <label for="show">Entries</label>
                    </form>
                </div>
                <div class="search">
                    <form action="" method="GET">
                        <input type="text" name="search" placeholder="Search..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" />
                        <button type="submit">Search</button>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No. <i class='bx bx-sort-up'></i></th>
                        <th>Position<i class='bx bx-sort-down'></i></th>
                        <th>Voter's Name <i class='bx bx-sort-up'></i></th>
                        <th>Candidate<i class='bx bx-sort-down'></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../db/db_connection.php'; // Include your DB connection

                    // Pagination and search logic
                    $entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;     // Fixed number of entries per page
                    $search = isset($_GET['search']) ? $_GET['search'] : ''; // Search query

                    // Calculate the current page and starting point
                    $page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1; // Default to page 1
                    $start = ($page - 1) * $entries; // Calculate the offset

                    // Query to get filtered votes with pagination
                    $sql = "SELECT 
                                votes.id AS vote_id,
                                CONCAT(user_voter.first_name, ' ', user_voter.last_name) AS voter_name,
                                positions.position_name,
                                CONCAT(candidates.first_name, ' ', candidates.last_name) AS candidate_name
                            FROM 
                                votes
                            JOIN 
                                user_voter ON votes.registrationNumber = user_voter.registrationNumber
                            JOIN 
                                positions ON votes.position_id = positions.id
                            JOIN 
                                candidates ON votes.candidate_id = candidates.id
                            WHERE 
                                CONCAT(user_voter.first_name, ' ', user_voter.last_name) LIKE ? OR
                                positions.position_name LIKE ? OR
                                CONCAT(candidates.first_name, ' ', candidates.last_name) LIKE ?
                            LIMIT ? OFFSET ?";

                    $stmt = $conn->prepare($sql);
                    $searchParam = '%' . $search . '%'; // Wildcard search
                    $stmt->bind_param('sssii', $searchParam, $searchParam, $searchParam, $entries, $start); // 3 strings, 2 integers
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $counter = $start + 1; // Adjust counter for pagination
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$counter}</td>
                                    <td>{$row['position_name']}</td>
                                    <td>{$row['voter_name']}</td>
                                    <td>{$row['candidate_name']}</td>
                                </tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>No votes found.</td></tr>";
                    }

                    // Close statement and connection
                    $stmt->close();
                    $conn->close();
                    ?>
                    </tbody>
                    </table>

            <!-- Pagination (optional) -->
            <div class="pagination">
                <?php
                include '../db/db_connection.php'; // Reopen connection for counting

                // Calculate the total number of pages
                $countSql = "SELECT COUNT(*) AS total FROM votes
                            JOIN user_voter ON votes.registrationNumber = user_voter.registrationNumber
                            JOIN positions ON votes.position_id = positions.id
                            JOIN candidates ON votes.candidate_id = candidates.id
                            WHERE CONCAT(user_voter.first_name, ' ', user_voter.last_name) LIKE ? OR
                                positions.position_name LIKE ? OR
                                CONCAT(candidates.first_name, ' ', candidates.last_name) LIKE ?";

                $countStmt = $conn->prepare($countSql);
                $countStmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                $totalRows = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalRows / $entries);

                
                // Display pagination controls
                if ($page > 1) {
                    echo "<button>  <a  style='text-decoration:none;' href='?page=" . ($page - 1) . "&search={$search}'><i class='bx bx-left-arrow'></i> Previous</a> </button>";
                }
                echo "<span> Page {$page} of {$totalPages} </span>";
                if ($page < $totalPages) {
                    echo "<button>  <a style='text-decoration:none;' href='?page=" . ($page + 1) . "&search={$search}'>Next <i class='bx bx-right-arrow'></i></a> </button>";
                }

                // Close connection for pagination count
                $countStmt->close();
                $conn->close();
                ?>
            </div>
            </div>
        </div>
    </div>
</section>

<script>
    function resetVotes() {
        if (confirm('Are you sure you want to reset all votes?')) {
            window.location.href = 'reset_votes.php'; // Assuming you have a reset_votes.php that handles resetting the votes.
        }
    }
</script>

</body>
</html>
