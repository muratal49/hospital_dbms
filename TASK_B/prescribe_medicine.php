<?php
session_start();

require_once 'db_config.php';

$conn = getConnection();

$message = "";

// Redirect if not logged in
if (!isset($_SESSION["doctor_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST['go_back_btn'])) {
  header('Location: caregiver_dashboard.php');
  exit();
}

$now = date('Y-m-d H:i:s');
$id = $_SESSION["doctor_id"];

// identify the patient the doctor is currently in appointment with
$find_appointment = $conn->query("SELECT id, patient_id, notes FROM appointment WHERE start < '$now' AND end > '$now' AND doctor_id = $id");
if ($find_appointment->num_rows == 0) {
  // case where the caregiver is not in an appointment - no medicine can be prescribed!
  $message = "There is no ongoing appointment!";
  $patient = "No patient available";
  $notes = "";
  $has_active_appointment = false;
} else {

  // case where the caregiver IS in an appointment
  $row = $find_appointment->fetch_assoc();
  $appointment_id = $row['id'];
  $patient_id = $row['patient_id'];
  $notes = $row['notes'];
  $has_active_appointment = true;

  $find_patient = $conn->query("SELECT first_name, last_name FROM patient WHERE id = $patient_id");
  $row = $find_patient->fetch_assoc();
  $patient_first = $row['first_name'];
  $patient_last = $row['last_name'];
  $patient = $patient_first . " " . $patient_last;

  if (isset($_POST["notes_btn"])) {
    $new_notes = $_POST["notes"];
    $update = $conn->prepare("UPDATE appointment SET notes = ? WHERE id = ?");
    $update->bind_param("si", $new_notes, $appointment_id);
    if ($update->execute()) {
      header('Location: caregiver_dashboard.php');
      exit();
    }
  }

  if (isset($_POST["prescribe_btn"])) {
    $medicine = $_POST["medicine"] ?? '';
    $dosage = $_POST["dosage"] ?? '';
    $expiration_date = $_POST["expiration_date"];

    // validate text inputs
    if ($medicine == '' || $dosage == '' || $expiration_date == '') {
      $message = 'All fields must be filled out!';
    }

    // validate expiration date
    if (strtotime($expiration_date) <= strtotime('today')) {
      $message = 'The expiration date cannot be in the past.';
    }

    if ($message == '') {
      $update = $conn->prepare("INSERT INTO prescription(name, dosage, expiration, appointment_id) VALUES (?, ?, ?, ?)");
      $update->bind_param("sssi", $medicine, $dosage, $expiration_date, $appointment_id);

      if ($update->execute()) {
        header('Location: caregiver_dashboard.php');
        exit();
      } else {
        $message = 'Error prescribing medicine: ' . $conn->error;
      }
    }
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Caregiver Portal - Prescribe Medicine & Make Appointment Notes</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>

  <h1>Caregiver Portal - Prescribe Medicine & Make Appointment Notes</h1>

  <!-- Error Message -->
  <?php if ($message != "") {
    echo "<div class=\"message-container\">
    <p class=\"error-message\">$message</p>
  </div>";
  } ?>

  <div class="container">
    <form class="form" method="post">
      <div class="form-row">
        <!-- LEFT -->
        <div class="form-group">
          <div class="label">Patient Name:</div>
          <div class="patient-name"><?php echo $patient; ?></div>
        </div>

        <!-- RIGHT -->
        <div class="form-group">
          <div class="label">Medicine:</div>
          <input type="text" placeholder="Tylenol" id="medicine" name="medicine">

          <div class="label" style="margin-top:15px;">Dosage:</div>
          <input type="text" placeholder="300 mg" id="dosage" name="dosage">

          <div class="label" style="margin-top:15px;">Expiration:</div>
          <input type="date" id="expiration_date" name="expiration_date">
        </div>
      </div>

      <div class="button-container">
        <button class="button" type="submit" name="prescribe_btn" <?php echo !$has_active_appointment ? 'disabled' : ''; ?>>
          Prescribe
        </button>
      </div>

      <div class="label" style="padding-top: 20px">Appointment notes:</div>
      <textarea rows="2" name="notes" id="notes">
        <?= $notes ?>
      </textarea>

      <div class="button-container">
        <button class="button" type="submit" name="notes_btn" <?php echo !$has_active_appointment ? 'disabled' : ''; ?>>
          Save
        </button>
      </div>
    </form>
  </div>

  <div class="button-container">
    <form method="post">
      <button class="backbutton" type="submit" name="go_back_btn">Back to Dashboard</button>
    </form>
  </div>

</body>

</html>