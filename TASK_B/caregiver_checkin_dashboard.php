<?php
session_start();

// Basic dashboard for caregiver after check-in. Shows patient info and past appointments with prescriptions.
require_once 'db_config.php';

// Ensure patient selected
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['patient_info'])) {
  header('Location: caregiver_patient_checkin.php');
  exit();
}

$patient = $_SESSION['patient_info'];
$history = $_SESSION['patient_history'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Caregiver Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .card { border: 1px solid #ddd; padding: 12px; margin-bottom: 12px; border-radius: 6px; }
    .header { margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
  </style>
</head>
<body>
  <h1 class="header">Caregiver Dashboard</h1>

  <div style="float:right;">
    <?php if (isset($_SESSION['doctor_id'])): ?>
      <?php
        // show doctor name
        $doc_name = '';
        $dbc = getConnection();
        $dstmt = $dbc->prepare('SELECT first_name, last_name FROM doctor WHERE id = ? LIMIT 1');
        if ($dstmt) {
          $dstmt->bind_param('i', $_SESSION['doctor_id']);
          $dstmt->execute();
          $dres = $dstmt->get_result();
          if ($dres && $dres->num_rows) {
            $drow = $dres->fetch_assoc();
            $doc_name = $drow['first_name'] . ' ' . $drow['last_name'];
          }
          $dstmt->close();
        }
        $dbc->close();
      ?>
      <div><strong>Doctor:</strong> <?= htmlspecialchars($doc_name) ?> | <a href="dashboard.php?logout=1">Logout</a></div>
    <?php elseif (isset($_SESSION['patient_id'])): ?>
      <?php
        $pname = '';
        $dbc = getConnection();
        $pstmt = $dbc->prepare('SELECT first_name, last_name FROM patient WHERE id = ? LIMIT 1');
        if ($pstmt) {
          $pstmt->bind_param('i', $_SESSION['patient_id']);
          $pstmt->execute();
          $pres = $pstmt->get_result();
          if ($pres && $pres->num_rows) {
            $prow = $pres->fetch_assoc();
            $pname = $prow['first_name'] . ' ' . $prow['last_name'];
          }
          $pstmt->close();
        }
        $dbc->close();
      ?>
      <div><strong>Patient:</strong> <?= htmlspecialchars($pname) ?> | <a href="dashboard.php?logout=1">Logout</a></div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>Patient</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
    <p><strong>DOB:</strong> <?= htmlspecialchars($patient['dob']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email'] ?? '') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone'] ?? '') ?></p>
    <?php if (isset($_SESSION['doctor_id'])): ?>
      <p>
        <a href="view_patient_history.php?patient_id=<?= urlencode($patient['id']) ?>">View full history (doctor view)</a>
      </p>
    <?php else: ?>
      <p><em>Doctor-only view available — <a href="login.php">log in as a doctor</a> to access full history.</em></p>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>Past Appointments</h2>
    <?php if (empty($history)): ?>
      <p>No past appointments found for this patient.</p>
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
    <a href="caregiver_patient_checkin.php">Back to check-in</a>
  </div>
</body>
</html>
