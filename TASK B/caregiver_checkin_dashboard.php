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

  <div class="card">
    <h2>Patient</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
    <p><strong>DOB:</strong> <?= htmlspecialchars($patient['dob']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email'] ?? '') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone'] ?? '') ?></p>
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
