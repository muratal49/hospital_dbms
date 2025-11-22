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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Caregiver Dashboard</title>
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <h1 class="header">Caregiver Dashboard</h1>

  <div class="form_container">
    <form class="/submit" method="post">
      <div class="form_body">

        <div class="form_field_container">

          <button type="submit" name="checkin_btn">Patient check-in</button>

          <button type="submit" name="prescribe_btn">Prescribe medicine</button>
        </div>
      </div>
    </form>
  </div>
</body>

</html>