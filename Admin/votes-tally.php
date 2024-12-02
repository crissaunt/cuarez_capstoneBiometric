<?php 

if (!isset($_SESSION['registeredNumber'])) {
    header("Location: login.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Counts</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
</head>


<body>
<div class="vote-counts">
    <div class="charts-grid" id="charts">
        <!-- Charts will be inserted dynamically -->
    </div>
    <?php include 'vote-table.php'; ?>
</div>

<script>
    Object.keys(chartData).forEach(position => {
        // Create a group container
        const groupContainer = document.createElement('div');
        groupContainer.className = 'position-group';
        groupContainer.innerHTML = `<h2>${position}</h2>`;
        document.getElementById('charts').appendChild(groupContainer);

        // Create a container for the chart
        const chartContainer = document.createElement('div');
        chartContainer.className = 'chart-container';
        chartContainer.innerHTML = `<canvas id="${position.replace(/\s+/g, '')}Chart"></canvas>`;
        groupContainer.appendChild(chartContainer);

        // Get data for the chart
        const labels = chartData[position].map(data => data.candidate);
        const votes = chartData[position].map(data => data.votes);

        // Generate random colors for each candidate
        const colors = labels.map(() => `#${Math.floor(Math.random() * 16777215).toString(16)}`);

        // Create the horizontal bar chart
        new Chart(document.getElementById(`${position.replace(/\s+/g, '')}Chart`), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Votes',
                    data: votes,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace(/../, "cc")),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Disable aspect ratio to let the chart fill its container
                indexAxis: 'y', // Horizontal bars
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: `${position} Votes`
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0,
                            align: 'start'
                        }
                    }
                }
            }
        });
    });
</script>

</body>
</html>
