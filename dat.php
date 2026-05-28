<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ESP32 Smart Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-4">
    <h2 class="text-center mb-4">ESP32 Ultrasonic Sensor Dashboard</h2>

    <!-- Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card text-white bg-primary">
          <div class="card-body">
            <h5 class="card-title">Current Distance</h5>
            <p class="card-text fs-3" id="distance">-- cm</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-success">
          <div class="card-body">
            <h5 class="card-title">LED Status</h5>
            <p class="card-text fs-3" id="ledStatus">OFF</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-danger">
          <div class="card-body">
            <h5 class="card-title">Buzzer Status</h5>
            <p class="card-text fs-3" id="buzzerStatus">OFF</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Table for sensor data -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">
          Sensor Data History
        </h5>
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-success">
            <tr>
              <th>Timestamp</th>
              <th>Distance (cm)</th>
              <th>LED (Red) Status</th>
              <th>Buzzer Status</th>
            </tr>
          </thead>
          <tbody id="sensorTable">
            <!-- Data rows will be inserted here -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Table for ESP32-CAM photos -->
    <div class="card">
      <div class="card-body">
       <!-- ESP32-CAM Photo Section -->
<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">ESP32-CAM Live Photo</h5>
    <div class="text-center">
      <img id="esp32camPhoto" src="http://your-server.com/photos/latest.jpg" 
           alt="ESP32-CAM Photo" class="img-fluid rounded shadow" width="400">
    </div>
  </div>
</div>
<img src="http://192.168.137.181:81/stream">
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    async function fetchData() {
      const response = await fetch("http://your-server.com/api/sensor/latest");
      const data = await response.json();

      // Update cards
      document.getElementById("distance").innerText = data.distance + " cm";
      document.getElementById("ledStatus").innerText = data.led ? "ON" : "OFF";
      document.getElementById("buzzerStatus").innerText = data.buzzer ? "ON" : "OFF";

      // Update sensor table
      const tableBody = document.getElementById("sensorTable");
      const row = `<tr>
        <td>${new Date().toLocaleString()}</td>
        <td><span class="fw-bold text-primary">${data.distance}</span></td>
        <td>${data.led ? "<span class='badge bg-danger'>ON</span>" : "<span class='badge bg-secondary'>OFF"}</td>
        <td>${data.buzzer ? "<span class='badge bg-warning text-dark'>ON</span>" : "<span class='badge bg-secondary'>OFF"}</td>
      </tr>`;
      tableBody.innerHTML = row + tableBody.innerHTML;

      // Update photo table (assuming API returns photo URL)
      if (data.photoUrl) {
        const photoBody = document.getElementById("photoTable");
        const photoRow = `<tr>
          <td>${new Date().toLocaleString()}</td>
          <td><img src="${data.photoUrl}" alt="ESP32-CAM Photo" width="200" class="img-thumbnail"></td>
        </tr>`;
        photoBody.innerHTML = photoRow + photoBody.innerHTML;
      }
    }

    setInterval(fetchData, 3000); // Refresh every 3s
  </script>
</body>
</html>
