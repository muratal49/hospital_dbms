<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

// Redirect if already logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

// Get current date time
$curr_datetime = date('Y-m-d H:i:s');

// Get upcoming appointments (prepared)
$up_stmt = $conn->prepare(
  "SELECT CONCAT(d.first_name,' ', d.last_name) AS doctor, a.start
   FROM appointment a
   JOIN doctor d ON a.doctor_id = d.id
   WHERE a.patient_id = ? AND a.start >= ?
   ORDER BY a.start ASC"
);
$result_upcoming = [];
if ($up_stmt) {
  $pid = $_SESSION['patient_id'];
  $up_stmt->bind_param('is', $pid, $curr_datetime);
  if ($up_stmt->execute()) {
    $res_up = $up_stmt->get_result();
    $result_upcoming = $res_up->fetch_all(MYSQLI_ASSOC);
  }
  $up_stmt->close();
}

// Get previous appointments (prepared)
$prev_stmt = $conn->prepare(
  "SELECT a.id, CONCAT(d.first_name,' ', d.last_name) AS doctor, a.start, CONCAT(p.name, ' ', p.dosage) AS prescription, a.notes
   FROM appointment a
   JOIN doctor d ON a.doctor_id = d.id
   LEFT JOIN prescription p ON a.id = p.appointment_id
   WHERE a.patient_id = ? AND a.start < ?
   ORDER BY a.start ASC"
);
$result_prev = [];
if ($prev_stmt) {
  $pid = $_SESSION['patient_id'];
  $prev_stmt->bind_param('is', $pid, $curr_datetime);
  if ($prev_stmt->execute()) {
    $res_prev = $prev_stmt->get_result();
    $result_prev = $res_prev->fetch_all(MYSQLI_ASSOC);
  }
  $prev_stmt->close();
}

// Format previous appointments to group prescriptions
$prev_aptmt_groups = [];

foreach ($result_prev as $aptmt) {
  $id = $aptmt['id'];

  // Create new appointment group
  if (!isset($prev_aptmt_groups[$id])) {
    $prev_aptmt_groups[$id] = [
      "doctor" => $aptmt['doctor'],
      'start' => $aptmt['start'],
      'prescriptions' => [],
      'notes' => $aptmt['notes']
    ];
  }

  // Add prescription
  if (isset($aptmt['prescription'])) {
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
  <h1 class="header">Patient Portal - View Appointments</h1>
  <div class="container-grid">
    <div class="top">
      <h4 class="top-header">Upcoming Appointments</h4>

      <div>
        <table class="table">
          <tr>
            <th><b>Doctor</b></th>
            <th><b>Date</b></th>
          </tr>
        </table>
      </div>

      <div class="scrollable table">
        <table class="table">
          <?php foreach ($result_upcoming as $aptmt): ?>
            <tr>
              <td><?= $aptmt['doctor'] ?></td>
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
            <th><b>Doctor</b></th>
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
              <td><?= $aptmt['doctor'] ?></td>
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