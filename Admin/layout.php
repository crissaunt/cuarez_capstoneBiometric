<?php

session_start();
if (!isset( $_SESSION['user'])) {
  header("Location: login.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dashboard</title>
  <!-- Link Styles -->
  <link rel="stylesheet" href="CSS/dashboard.css">
  <link rel="stylesheet" href="CSS/table.css">
  <link rel="stylesheet" href="CSS/modal.css">
 
  
  <link href="../boxicons-master/css/boxicons.min.css" rel="stylesheet">

  <!-- <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> -->
</head>
<body>
<script src="JS/script.js" ></script>
<script src="JS/modal.js" ></script>


  <div class="sidebar">
    <div class="logo_details">
      <div class="logo_name">Admin</div>
      <i class="bx bx-menu" id="btn"></i>
    </div>
    <ul class="nav-list">
    
      <li> 
        <a href="dashboard.php">
          <i class="bx bx-grid-alt"></i>
          <span class="link_name">Dashboard</span>
        </a>
        <span class="tooltip">Dashboard</span>
      </li>
      <li>
        <a href="votes-page.php">
          <i class='bx bxs-note'></i>
          <span class="link_name">Votes</span>
        </a>
        <span class="tooltip">Votes</span>
      </li>
      <li>
        <a href="voter-page.php">
          <i class='bx bx-male-female'></i>
          <span class="link_name">Students/Voters</span>
        </a>
        <span class="tooltip">Students/Voters </span>
      </li>
      <li>
        <a href="position.php">
            <i class='bx bxs-city'></i>
          <span class="link_name">Position</span>
        </a>
        <span class="tooltip">Positions</span>
      </li>
      <li>
        <a href="candidates.php">
          <i class='bx bx-street-view'></i>
          <span class="link_name">Candidates</span>
        </a>
        <span class="tooltip">Candidates</span>
      </li>
      <li>
        <a href="#">
          <i class='bx bxs-square-rounded'></i>
          <span class="link_name">Election Title</span>
        </a>
        <span class="tooltip">Election Title</span>
      </li>

      <li>
          <a href="#" onclick="confirmLogout()">
          <i class='bx bx-horizontal-left'></i>
              <span class="link_name">Logout</span>
          </a>
          <span class="tooltip">Logout</span>
      </li>
    </ul>
  </div>

  <script>
    function confirmLogout() {
        var confirmAction = confirm("Are you sure you want to logout?");
        if (confirmAction) {
            // Redirect to the logout page
            window.location.href = "logout.php";
        }
    }
</script>