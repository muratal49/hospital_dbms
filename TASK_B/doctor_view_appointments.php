<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

// Redirect if not logged in
if (!isset($_SESSION["doctor_id"])) {
  header('Location: login.php');
  exit();
}

// Get current date time
$curr_datetime = date('Y-m-d H:i:s');

// Get upcoming appointments
$sql = "SELECT 
          CONCAT(p.first_name,' ', p.last_name) AS patient,
          a.start
        FROM appointment a
        JOIN patient p ON a.patient_id = p.id
        WHERE 
          a.doctor_id = '{$_SESSION['doctor_id']}' AND
          a.start >= '$curr_datetime'
        ORDER BY a.start DESC";
$query = $conn->query($sql);
$result_upcoming = $query->fetch_all(MYSQLI_ASSOC);

// Get previous appointments
$sql = "SELECT 
          a.id,
          CONCAT(p.first_name,' ', p.last_name) AS patient, 
          a.start, 
          CONCAT(pr.name, ' ', pr.dosage) AS prescription,
          a.notes
        FROM appointment a
        JOIN patient p ON a.patient_id = p.id
        LEFT JOIN prescription pr ON a.id = pr.appointment_id
        WHERE 
          a.doctor_id = '{$_SESSION['doctor_id']}' AND
          a.start < '$curr_datetime'
        ORDER BY a.start DESC";

$query = $conn->query($sql);
$result_prev = $query->fetch_all(MYSQLI_ASSOC);

// Format previous appointments to group prescriptions
$prev_aptmt_groups = [];

foreach ($result_prev as $aptmt) {
  $id = $aptmt['id'];

  // Create new appointment group
  if (!isset($prev_aptmt_groups[$id])) {
    $prev_aptmt_groups[$id] = [
      "patient" => $aptmt['patient'],
      'start' => $aptmt['start'],
      'prescriptions' => [],
      'notes' => $aptmt['notes']
    ];
  }

  // Add prescription (could be null so need to check for that)
  if (!empty($aptmt['prescription'])) {
    $prev_aptmt_groups[$id]['prescriptions'][] = $aptmt['prescription'];
  }
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Appointments</title>
  <link rel="stylesheet" href="patient_view_appointments.css" />
</head>

<body>
  <h1 class="header">Doctor Portal - View Appointments</h1>
  <div class="container-grid">
    <div class="top">
      <h4 class="top-header">Upcoming Appointments</h4>

      <div>
        <table class="table">
          <tr>
            <th><b>Patient</b></th>
            <th><b>Date</b></th>
          </tr>
        </table>
      </div>

      <div class="scrollable table">
        <table class="table">
          <?php foreach ($result_upcoming as $aptmt): ?>
            <tr>
              <td><?= $aptmt['patient'] ?></td>
              <td><?= $aptmt["start"] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>

    <div class="bottom">
      <h4 class="top-header">Past Appointments</h4>

      <div>
        <table class="table">
          <tr>
            <th><b>Patient</b></th>
            <th><b>Date</b></th>
            <th><b>Prescriptions</b></th>
            <th><b>Notes</b></th>
          </tr>
        </table>
      </div>

      <div class="scrollable table">
        <table class="table">
          <?php foreach ($prev_aptmt_groups as $aptmt): ?>
            <tr>
              <td><?= $aptmt['patient'] ?></td>
              <td><?= $aptmt['start'] ?></td>
              <td>
                <?php foreach ($aptmt['prescriptions'] as $prescription): ?>
                  <?= $prescription ?><br />
                <?php endforeach; ?>
              </td>
              <td><?= $aptmt['notes'] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>

    </div>
  </div>
</body>

</html>