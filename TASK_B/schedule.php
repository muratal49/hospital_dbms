<?php
session_start();

require_once 'db_config.php';

$conn = getConnection();

$message = "";
$departmentAvailability = [];

// Redirect if already logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
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

    if ($row['count'] == 0) {
      $message = 'Invalid department';
    }
  }

  // Query doctors in department
  if ($message == '') {
    $availableSlots = getAvailableSlots($conn, $department, $datestr);

    if (count($availableSlots) == 0) {
      $message = 'No availability found';
    } else {
      $departmentAvailability = $availableSlots;
    }
  }
}

if (isset($_POST['schedule_btn'])) {
  $doctor_id = trim($_POST['id']);
  $datetime_str = trim($_POST['datetime']);
  $message = '';

  if ($doctor_id == '' || $datetime_str == '') {
    $message = 'Please fill in all fields';
  }

  // Validate datetime and that it is within working hours
  if ($message == '') {
    $datetime_obj = DateTime::createFromFormat('Y-m-d\TH:i', $datetime_str);

    // Check valid date time and within working hours
    if ($datetime_obj === false || $datetime_obj->format('Y-m-d\TH:i') !== $datetime_str) {
      $message = 'Enter a valid date and time';

    } else if ($datetime_obj->format('H:i') < '09:00' || $datetime_obj->format('H:i') > '20:00') {
      $message = 'Appointment time must be between working hours (09:00 - 20:00)';
    }
  }

  // Validate it is an actual doctor id
  if ($message == '') {
    $sql = "SELECT COUNT(*) FROM doctor WHERE id = '$doctor_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
      $message = 'Invalid doctor ID';
    }
  }

  // Check appointment availability
  if ($message == '') {
    // Get start and end datetime strings
    $start_str = $datetime_obj->format('Y-m-d H:i:s');
    $end_obj = clone $datetime_obj;
    $end_obj->modify('+30 minutes');
    $end_str = $end_obj->format('Y-m-d H:i:s');

    $sql = "SELECT COUNT(*) from appointment
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
      $message = 'Appointment scheduled successfully';
    } else {
      $message = 'Error scheduling appointment: ' . $conn->error;
    }
  }
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

    <div class="container">
      <div class="left-top">
        <!-- Schedule form -->
        <form class="form" method="post">
          <div class="form_body">
            <h2 class="form_header">Search for Available Doctors</h2>

            <div class="form_field_container">
              <label for="department">Department:</label>
              <input
                class="form_field"
                type="text"
                id="department"
                name="department"
              />

              <label for="date">Date:</label>
              <input class="form_field" type="date" id="date" name="date" />

              <button class="form_button" type="submit" name="search_btn">
                View Available Times
              </button>
            </div>
          </div>
        </form>
      </div>
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
      </div>
    </div>
  </body>
</html>
