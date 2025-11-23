<?php
session_start();

if (!isset($_SESSION["doctor_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST["checkin_btn"])) {
  header('Location: caregiver_patient_checkin.php');
  exit();
} else if (isset($_POST["prescribe_btn"])) {
  header('Location: prescribe_medicine.php');
  exit();
} else if (isset($_POST["logout_btn"])) {
  unset($_SESSION["doctor_id"]);
  header('Location: login.php');
  exit();
} else if (isset($_POST['view_appointments_btn'])) {
  header('Location: doctor_view_appointments.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Caregiver Dashboard</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="dashboard.css" />
</head>

<body>
  <h1 class="header">Caregiver Dashboard</h1>

  <div class="grid-container">
    <form class="grid-item" method="post">
      <button type="submit" name="checkin_btn">Patient check-in</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="prescribe_btn">Prescribe medicine</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="logout_btn">Log out</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="view_appointments_btn">View Appointments</button>
    </form>
  </div>
</body>

</html>