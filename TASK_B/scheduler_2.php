<?php
session_start();

require_once 'db_config.php';
require_once 'helper.php';

$conn = getConnection();

$message = "";
$departmentAvailability = [];
$upcomingAppointments = [];

// Redirect if already logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST['delete_btn'])) {
  $appointment_id = trim($_POST['appointment_id']);

  if ($appointment_id == '') {
    $message = 'Please select a valid appointment to delete';
  }

  if ($message == '') {
    $appointment_id = intval($appointment_id);

    $stmt = $conn->prepare("SELECT id FROM appointment WHERE id = ? AND patient_id = ? AND start > NOW()");

    if ($stmt === false) {
      $message = 'Unable to prepare appointment lookup: ' . $conn->error;
    } else {
      $stmt->bind_param('ii', $appointment_id, $_SESSION['patient_id']);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows == 0) {
        $message = 'Unable to delete appointment';
      }

      $stmt->close();
    }
  }

  if ($message == '') {
    $stmt = $conn->prepare("DELETE FROM appointment WHERE id = ? AND patient_id = ?");

    if ($stmt === false) {
      $message = 'Unable to prepare delete statement: ' . $conn->error;
    } else {
      $stmt->bind_param('ii', $appointment_id, $_SESSION['patient_id']);

      if ($stmt->execute()) {
        $message = 'Appointment deleted successfully';
      } else {
        $message = 'Error deleting appointment: ' . $stmt->error;
      }

      $stmt->close();
    }
  }
}

if (isset($_POST['search_btn'])) {
  $department = trim($_POST['department']);
  $datestr = trim($_POST['date']);
  $message = '';

  if ($department == '' || $datestr == '') {
    $message = 'Please fill in all fields';
  }

  // Validate time
  if ($message == '') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $datestr);

    // Not in date format or not a valid date
    if ($date_obj === false || $date_obj->format('Y-m-d') !== $datestr) {
      $message = 'Enter a valid date';
    }
  }

  // Validate it is an actual department
  if ($message == '') {
    $sql = "SELECT COUNT(*) as count FROM department WHERE name = '$department'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

@@ -102,50 +149,73 @@ if (isset($_POST['schedule_btn'])) {
            WHERE doctor_id = '$doctor_id' 
            AND start < '$end_str' AND end > '$start_str'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row["count"] > 0) {
      $message = "This time slot is not available. Try a different time.";
    }
  }

  // Schedule appointment
  if ($message == '') {
    $sql = "INSERT INTO appointment (doctor_id, patient_id, start, end)
            VALUES ('$doctor_id', '{$_SESSION['patient_id']}', '$start_str', '$end_str')";

    if ($conn->query($sql) === TRUE) {
      header('Location: patient_dashboard.php');
      exit();
    } else {
      $message = 'Error scheduling appointment: ' . $conn->error;
    }
  }
}


// Get upcoming appointments for current patient
$sql = "SELECT
          a.id,
          CONCAT(d.first_name, ' ', d.last_name) AS doctor,
          a.start
        FROM appointment a
        JOIN doctor d ON a.doctor_id = d.id
        WHERE
          a.patient_id = ? AND
          a.start > NOW()
        ORDER BY a.start ASC";

$stmt = $conn->prepare($sql);

if ($stmt !== false) {
  $stmt->bind_param('i', $_SESSION['patient_id']);
  $stmt->execute();
  $result = $stmt->get_result();
  $upcomingAppointments = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="schedule_styles.css" />
  </head>

  <body>
    <h1 class="header">Patient Portal - Query for Available Doctors</h1>

    <!-- <div class="message-container">
      <p class="error-message">Error with something</p>
    </div> -->

    <!-- Error Message -->
    <?php if ($message != "") {
      echo "<div class=\"message-container\">
        <p class=\"error-message\">$message</p>
      </div>";
    } ?>

@@ -178,49 +248,73 @@ $conn->close();
      <div class="left-bottom">
        <form class="form" method="post">
          <div class="form_body">
            <h2 class="form_header">Schedule an Appointment</h2>

            <div class="form_field_container">
              <label for="id">Doctor ID:</label>
              <input
                class="form_field"
                type="text"
                id="id"
                name="id"
              />

              <label for="datetime">Date and Time:</label>
              <input class="form_field" type="datetime-local" id="datetime" name="datetime" />
              
              <button class="form_button" type="submit" name="schedule_btn">
                Schedule 30 min
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="right">
        <h2 class="form_header">Available Doctors</h2>
        <table class="table">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Times</th>
          </tr>
          <?php foreach ($departmentAvailability as $availability): ?>
          <tr>
            <td><?= $availability["id"] ?></td>
            <td><?= $availability["doctor"] ?></td>
            <td>
              <?php foreach ($availability["times"] as $slot): ?>
              <?= $slot["start"] ?>
              -
              <?= $slot["end"] ?><br/>
              <?php endforeach; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>

        <h2 class="form_header">Your Upcoming Appointments</h2>
        <table class="table">
          <tr>
            <th>ID</th>
            <th>Doctor</th>
            <th>Start</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($upcomingAppointments as $appointment): ?>
          <tr>
            <td><?= $appointment['id'] ?></td>
            <td><?= $appointment['doctor'] ?></td>
            <td><?= $appointment['start'] ?></td>
            <td>
              <form method="post" style="margin: 0;">
                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>" />
                <button class="form_button" type="submit" name="delete_btn">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </body>
</html>
