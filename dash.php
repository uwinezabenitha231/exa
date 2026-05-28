<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ESP32-CAM Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body{
      background:#f4f4f4;
    }

    .camera-box{
      text-align:center;
    }

    .camera-box img{
      width:100%;
      max-width:700px;
      border-radius:10px;
      border:5px solid #0d6efd;
    }
  </style>

</head>

<body>

<div class="container my-4">

  <h2 class="text-center mb-4">
    ESP32-CAM Control Dashboard
  </h2>

  <!-- CAMERA STREAM -->
  <div class="card mb-4">

    <div class="card-header bg-danger text-white">
      Live Camera Stream
    </div>

    <div class="card-body camera-box">

      

    </div>
  </div>

  <!-- SENSOR TABLE -->
  <div class="card mb-4">

    <div class="card-header bg-primary text-white">
      Latest Sensor Data
    </div>

    <div class="card-body">

      <table class="table table-striped table-bordered">

        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Distance (cm)</th>
            <th>Relay</th>
            <th>Servo Angle</th>
            <th>LED Status</th>
            <th>Timestamp</th>
          </tr>
        </thead>

        <tbody>

          <?php

          $conn = new mysqli("localhost", "root", "", "sarah_muhayimana");

          if ($conn->connect_error) {
            die("<tr><td colspan='6'>Connection failed: " . $conn->connect_error . "</td></tr>");
          }

          $result = $conn->query("SELECT * FROM ultra_data ORDER BY id DESC LIMIT 20");

          while($row = $result->fetch_assoc()) {

            echo "<tr>
                    <td>".$row['id']."</td>
                    <td>".$row['distance']."</td>
                    <td>".($row['relay_state'] ? 'ON' : 'OFF')."</td>
                    <td>".$row['servo_angle']."</td>
                    <td>".$row['led_status']."</td>
                    <td>".$row['timestamp']."</td>
                  </tr>";
          }

          $conn->close();

          ?>

        </tbody>

      </table>

    </div>
  </div>

  <!-- CHART -->
  <div class="card">

    <div class="card-header bg-success text-white">
      Distance Graph
    </div>

    <div class="card-body">

      <canvas id="distanceChart"></canvas>

    </div>

  </div>

</div>

<?php

$conn = new mysqli("localhost", "root", "", "sarah_muhayimana");

$distances = [];
$timestamps = [];

if (!$conn->connect_error) {

  $result = $conn->query("SELECT distance, timestamp FROM ultra_data ORDER BY id DESC LIMIT 20");

  while($row = $result->fetch_assoc()) {

    $distances[] = $row['distance'];
    $timestamps[] = $row['timestamp'];

  }

  $conn->close();
}

?>

<script>

const ctx = document.getElementById('distanceChart').getContext('2d');

new Chart(ctx, {

  type: 'line',

  data: {

    labels: <?php echo json_encode($timestamps); ?>,

    datasets: [{

      label: 'Distance (cm)',

      data: <?php echo json_encode($distances); ?>,

      borderColor: 'blue',

      backgroundColor: 'rgba(0, 123, 255, 0.2)',

      fill: true,

      tension: 0.3

    }]
  },

  options: {

    responsive: true,

    plugins: {
      legend: {
        display: true
      }
    },

    scales: {

      x: {
        title: {
          display: true,
          text: 'Time'
        }
      },

      y: {
        title: {
          display: true,
          text: 'Distance (cm)'
        }
      }

    }
  }
});

</script>

</body>
</html>