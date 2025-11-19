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

  // Query doctors in department
  if ($message == '') {
    $sql = "SELECT doc.id, doc.first_name, doc.last_name
            FROM department dept
            JOIN doctor doc on doc.department_id = dept.id
            WHERE dept.name = '$department'";
    $result = $conn->query($sql);
    $doctors = $result->fetch_all(MYSQLI_ASSOC);

    if (count($doctors) == 0) {
      $message = 'Invalid department';
    }
  }

  // Main logic
  if ($message == '') {
    $departmentAvailability = [];

    foreach ($doctors as $doctor) {
      $doctor_id = $doctor['id'];
      $doctor_name = $doctor['first_name'] . ' ' . $doctor['last_name'];
      $availableTimes = [];

      // Query appointments from doctors
      $sql = "SELECT a.start, a.end 
              FROM appointment a
              WHERE 
                a.doctor_id = '$doctor_id' AND
                a.start BETWEEN '$datestr' AND DATE_ADD('$datestr', INTERVAL 24 HOUR)";

      $result = $conn->query($sql);
      $aptmpts = $result->fetch_all(MYSQLI_ASSOC);

      // Get open time slots
      $curr = new DateTime("$datestr 09:00:00");
      $end = new DateTime("$datestr 20:00:00");

      $availableTimes = [];
      foreach ($aptmts as $aptmt) {
        $aptmt_start = new DateTime($aptmt["start"]);
        $aptmt_end = new DateTime($aptmt["end"]);

        // Have a free block if there is time between now and appointment
        if ($aptmt_start > $curr) {
          array_push($availableTimes, [
            "doctor" => $doctor,
            "start" => $curr->format("H:i"),
            "end" => $aptmt_start->format("H:i")
          ]);
        }

        // Update next interval start time
        if ($aptmt_end > $curr) {
          $curr = $aptmt_end;
        }
      }

      // Check if there is remaining time after lost appointment
      if ($curr < $end) {
        array_push($availableTimes, [
          "doctor" => $doctor,
          "start" => $curr->format("H:i"),
          "end" => $end->format("H:i")
        ]);
      }

      if (!empty($availableTimes)) {
        $departmentAvailability[$doctor_name] = $availableTimes;
      }
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
  <?php if ($message != "") {
    echo "<p>$message<p>";
  } ?>

  <h1 class="header">Patient Portal - Query for Available Doctors</h1>

  <div class="form_container">
    <!-- Schedule form -->
    <form class="form" method="post">
      <div class="form_body">
        <h2 class="form_header">Schedule an Appointment</h2>

        <div class="form_field_container">
          <label for="department">Department:</label>
          <input class="form_field" type="text" id="department" name="department" />

          <label for="datetime">Date:</label>
          <input class="form_field" type="date" id="date" name="date" />

          <button class="form_button" type="submit" name="search_btn">
            View Available Times
          </button>
        </div>
      </div>
    </form>
  </div>
<!-- 
  <table class="table">
    <tr>
      <th>Doctor</th>
      <th>Times</th>
    </tr>
    <tr>
      <td>Joe Baloney</td>
      <td>11:30-12:00pm</td>
    </tr>
    <tr>
      <td>Joe Baloney</td>
      <td>11:30-12:00pm <br> 10</td>
    </tr>
  </table> -->

  <table class="table">
    <tr>
      <th>Doctor</th>
      <th>Times</th>
    </tr>
    <?php foreach ($departmentAvailability as $doctor_name => $slots): ?>
      <tr>
        <td><?= $doctor_name ?></td>
        <td>
          <?php foreach ($slots as $slot): ?>
            <?= $slot["start" ]?> - <?= $slot["end"]?><br>
          <?php endforeach; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>