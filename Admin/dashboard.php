<?php include 'layout.php'; ?>
  <section class="home-section">
    <div class="text">
        <div class="dashboard">
            <h1>Dashboard</h1>
            <div class="row">
                <?php 

                include '../db/db_connection.php';


                $totalvotes = "SELECT COUNT(*) AS total FROM votes";
                $totalvotesResult = $conn->query($totalvotes);

                if ($totalvotesResult->num_rows > 0) {
                    $row = $totalvotesResult->fetch_assoc();
                    $totalvotes = $row['total'];
                }
                ?>


                <div class="md">
                    <div class="inner">
                        <h3><?php echo $totalvotes ?></h3>
                        <p>No. of Votes</p>
                    </div>
                    <div class="icon">
                        <i class='bx bxs-note'></i>
                    </div>
                </div>



                <?php 

                include '../db/db_connection.php';


                $totaluserVoter = "SELECT COUNT(*) AS total FROM user_voter";
                $totaluserVoterResult = $conn->query($totaluserVoter);

                if ($totaluserVoterResult->num_rows > 0) {
                    $row = $totaluserVoterResult->fetch_assoc();
                    $totaluserVoterResult = $row['total'];
                }
                ?>


                <div class="md">
                    <div class="inner">
                        <h3><?php echo $totaluserVoterResult ?></h3>
                        <p>No. of Students</p>
                    </div>
                    <div class="icon">
                        <i class='bx bx-male-female'></i>  
                    </div>
                </div>






                <?php 

                include '../db/db_connection.php';


                $totalPosition = "SELECT COUNT(*) AS total FROM positions";
                $totalPositionResult = $conn->query($totalPosition);

                if ($totalPositionResult->num_rows > 0) {
                    $row = $totalPositionResult->fetch_assoc();
                    $totalPositionResultResult = $row['total'];
                }
                ?>
                <div class="md">
                    <div class="inner">
                        <h3><?php echo $totalPositionResultResult ?></h3>
                        <p>No. of Positions</p>
                    </div>
                    <div class="icon">
                        <i class='bx bxs-city'></i> 
                    </div>
    
                </div>

                <?php 

                    include '../db/db_connection.php';


                    $totalCandidates = "SELECT COUNT(*) AS total FROM candidates";
                    $totalCandidatesResult = $conn->query($totalCandidates);

                    if ($totalCandidatesResult->num_rows > 0) {
                        $row = $totalCandidatesResult->fetch_assoc();
                        $totalCandidatesResult = $row['total'];
                    }
                    ?>






                <div class="md">
                    <div class="inner">
                        <h3><?php echo $totalCandidatesResult ?></h3>
                        <p>No. of Candidates</p>
                    </div>
                    <div class="icon">
                        <i class='bx bx-street-view'></i>    
                    </div>
                </div>
            </div>
            <h1>Votes Tally</h1>
           
        </div>
        
    </div>
    <div class="row">
                <?php include 'votes-tally.php'; ?>

    </div>
            
    
  </section>
  <!-- Scripts -->

            
</body>
</html>