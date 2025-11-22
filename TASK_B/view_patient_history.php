<?php
session_start();

require_once 'db_config.php';

$conn = getConnection();

// Only allow doctors/caregivers to view this page
if (!isset($_SESSION['doctor_id'])) {
  header('Location: login.php');
  exit();
}

$patient_id = isset($_GET['checked_in_patient_id']) ? intval($_GET['checked_in_patient_id']) : 0;
if ($patient_id <= 0) {
  echo 'Invalid patient id';
  exit();
}

// Fetch patient basic info
$stmt = $conn->prepare("SELECT id, first_name, last_name, dob, phone, email FROM patient WHERE id = ? LIMIT 1");
if ($stmt === false) {
  echo 'DB prepare error';
  exit();
}
$stmt->bind_param('i', $patient_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
  echo 'Patient not found';
  exit();
}
$patient = $res->fetch_assoc();
$stmt->close();

// Fetch past appointments and prescriptions
$curr_datetime = date('Y-m-d H:i:s');
$hist_stmt = $conn->prepare(
  "SELECT a.id, a.start, a.notes, CONCAT(d.first_name, ' ', d.last_name) AS doctor, CONCAT(p.name, ' ', p.dosage) AS prescription
   FROM appointment a
   JOIN doctor d ON a.doctor_id = d.id
   LEFT JOIN prescription p ON a.id = p.appointment_id
   WHERE a.patient_id = ?
   ORDER BY a.start DESC"
);

$patient_history = [];
if ($hist_stmt !== false) {
  $hist_stmt->bind_param('i', $patient_id);
  if ($hist_stmt->execute()) {
    $hist_res = $hist_stmt->get_result();
    while ($h = $hist_res->fetch_assoc()) {
      $aid = $h['id'];
      if (!isset($patient_history[$aid])) {
        $patient_history[$aid] = [
          'id' => $aid,
          'doctor' => $h['doctor'],
          'start' => $h['start'],
          'notes' => $h['notes'],
          'prescriptions' => []
        ];
      }

      if (!empty($h['prescription'])) {
        $patient_history[$aid]['prescriptions'][] = $h['prescription'];
      }
    }
  }
  $hist_stmt->close();
}

$conn->close();

$history = array_values($patient_history);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>View Patient History</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .card { border: 1px solid #ddd; padding: 12px; margin-bottom: 12px; border-radius: 6px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
  </style>
</head>
<body>
  <h1>Patient History (Doctor View)</h1>
  <div class="card">
    <h2>Patient</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
    <p><strong>DOB:</strong> <?= htmlspecialchars($patient['dob']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email'] ?? '') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone'] ?? '') ?></p>
  </div>

  <div class="card">
    <h2>Appointments</h2>
    <?php if (empty($history)): ?>
      <p>No appointments found for this patient.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Doctor</th>
            <th>Date</th>
            <th>Prescriptions</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $apt): ?>
            <tr>
              <td><?= htmlspecialchars($apt['doctor']) ?></td>
              <td><?= htmlspecialchars($apt['start']) ?></td>
              <td>
                <?php if (!empty($apt['prescriptions'])): ?>
                  <?php foreach ($apt['prescriptions'] as $p): ?>
                    <?= htmlspecialchars($p) ?><br />
                  <?php endforeach; ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($apt['notes'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div>
    <a href="javascript:history.back()">Back</a>
  </div>
</body>
</html>
