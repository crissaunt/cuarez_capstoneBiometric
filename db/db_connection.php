<?php
$conn = new mysqli ('localhost','root' ,'','voting_system');

if($conn->connect_error){
    echo "Seems like you have not configured the database. Failed To Connect to database:" . $conn->connect_error;
}
?>