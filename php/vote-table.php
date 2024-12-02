<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "voting_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch positions ordered by priority
$positions = $conn->query("SELECT * FROM positions ORDER BY priority ASC");

// Data for Chart.js
$chartData = [];

while ($position = $positions->fetch_assoc()) {
    $position_id = $position['id'];
    $position_name = $position['position_name'];
    
    echo "<h2>{$position_name}</h2>";
    
    // Query to fetch candidates for this position
    $candidates = $conn->query("
        SELECT c.first_name, c.last_name, COUNT(v.id) AS vote_count 
        FROM candidates c 
        LEFT JOIN votes v ON c.id = v.candidate_id 
        WHERE c.position_id = $position_id 
        GROUP BY c.id
    ");

    $chartData[$position_name] = []; // Initialize data for this position

    echo "<table >";
    echo "<thead><tr><th>Candidate</th><th>Votes</th></tr></thead>";
    echo "<tbody>";

    while ($candidate = $candidates->fetch_assoc()) {
        $full_name = $candidate['first_name'] . " " . $candidate['last_name'];
        $vote_count = $candidate['vote_count'] ?: 0; // Handle null vote count

        // Add to chart data
        $chartData[$position_name][] = [
            "candidate" => $full_name,
            "votes" => $vote_count
        ];

        echo "<tr>
                <td>{$full_name}</td>
                <td>{$vote_count}</td>
              </tr>";
    }

    echo "</tbody></table>";

}

$conn->close();

// Encode data for JavaScript
echo "<script>const chartData = " . json_encode($chartData) . ";</script>";
?>
