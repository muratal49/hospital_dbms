<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST["schedule_apt_btn"])) {
  header('Location: schedule.php');
  exit();
} else if (isset($_POST["view_apt_btn"])) {
  header('Location: patient_view_appointments.php');
  exit();
} else if (isset($_POST["update_info_btn"])) {
  header('Location: update_information.php');
  exit();
} else if (isset($_POST["logout_btn"])) {
  unset($_SESSION["patient_id"]);
  header('Location: login.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Patient Dashboard</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="dashboard.css" />
</head>

<body>
  <h1 class="header">Patient Dashboard</h1>

  <div class="grid-container">
    <form class="grid-item" method="post">
      <button type="submit" name="schedule_apt_btn">Schedule appointments</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="view_apt_btn">View appointments</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="update_info_btn">Update information</button>
    </form>
    <form class="grid-item" method="post">
      <button type="submit" name="logout_btn">Log out</button>
    </form>
  </div>
</body>

</html>