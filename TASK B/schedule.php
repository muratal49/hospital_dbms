<?php
session_start();

$servername = "localhost";
$username = "ezhupa"; // use your own username
$password = "...";    // use your own password
$dbname = "ezhupa_1"; // use your own database name
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function getAvailableSlots($conn, $department, $datestr): array
{
  $departmentAvailability = [];

  // Get appointments from doctors in the department
  $sql = "SELECT 
            doc.id as doctor_id, 
            CONCAT(doc.first_name, ' ', doc.last_name) as doctor_name,
            apt.start, apt.end
          FROM Doctor doc
          JOIN Department dpt on doc.department_id = dpt.id
          LEFT JOIN Appointment apt on doc.id = apt.doctor_id AND
            apt.start >= '$datestr 09:00:00' AND 
            apt.end <= '$datestr 20:00:00'
          WHERE 
            dpt.name = '$department'
          ORDER BY doc.id, apt.start";
  $result = $conn->query($sql);
  $rows = $result->fetch_all(MYSQLI_ASSOC);

  $doctor_schedules = [];

  // Create map for doctor id -> appointments (helps with handling NULL)
  foreach ($rows as $row) {
    $id = $row["doctor_id"];

    if (!isset($doctor_schedules[$id])) {
      $doctor_schedules[$id] = [
        'name' => $row['doctor_name'],
        'appointments' => []
      ];
    }

    if ($row['start'] != NULL) {
      $doctor_schedules[$id]['appointments'][] = [
        'start' => $row['start'],
        'end' => $row['end']
      ];
    }
  }


  foreach ($doctor_schedules as $doc_id => $doc_data) {
    $availableTimes = [];

    // Get open time slots
    $curr = new DateTime("$datestr 09:00:00");
    $end = new DateTime("$datestr 20:00:00");

    foreach ($doc_data['appointments'] as $aptmt) {
      $aptmt_start = new DateTime($aptmt["start"]);
      $aptmt_end = new DateTime($aptmt["end"]);

      // Free block between now and next appointment
      if ($aptmt_start > $curr) {
        $availableTimes[] = [
          "start" => $curr->format("H:i"),
          "end" => $aptmt_start->format("H:i")
        ];
      }

      // Update next interval start time
      if ($aptmt_end > $curr) {
        $curr = $aptmt_end;
      }
    }

    // Check if there is remaining time after last appointment
    if ($curr < $end) {
      $availableTimes[] = [
        "start" => $curr->format("H:i"),
        "end" => $end->format("H:i")
      ];
    }

    if (!empty($availableTimes)) {
      $departmentAvailability[] = [
        "id" => $doc_id,
        "doctor" => $doc_data["name"],
        "times" => $availableTimes
      ];
    }
  }

  return $departmentAvailability;
}

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
    $sql = "SELECT COUNT(*) as count FROM Department WHERE name = '$department'";
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

    } else if ($datetime_obj->format('H:i') < '09:00' || $datetime_obj->format('H:i') > '19:30') {
      $message = 'Appointment time must be between working hours (09:00 - 20:00)';
    }
  }

  // Validate it is an actual doctor id
  if ($message == '') {
    $sql = "SELECT COUNT(*) as count FROM Doctor WHERE id = '$doctor_id'";
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

    $sql = "SELECT COUNT(*) as count from Appointment
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
    $sql = "INSERT INTO Appointment (doctor_id, patient_id, start, end)
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
