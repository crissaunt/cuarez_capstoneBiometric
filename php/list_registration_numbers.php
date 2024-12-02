<?php
$directory = '../images';
$registrationNumbers = array_diff(scandir($directory), array('..', '.'));
echo json_encode(array_values($registrationNumbers)); // Return JSON array of registration numbers
?>
