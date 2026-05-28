<?php
$distance = isset($_GET['distance']) ? $_GET['distance'] : 0;
$relay    = isset($_GET['relay']) ? $_GET['relay'] : 0;
$servo    = isset($_GET['servo']) ? $_GET['servo'] : 0;
$led      = isset($_GET['led']) ? $_GET['led'] : 'OFF';

$conn = new mysqli("localhost", "root", "", "sarah_muhayimana");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO ultra_data (distance, relay_state, servo_angle, led_status)
        VALUES ('$distance', '$relay', '$servo', '$led')";
if ($conn->query($sql) === TRUE) {
  echo "Data inserted";
} else {
  echo "Error: " . $conn->error;
}
$conn->close();
?>
