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
$availableTimes = [];

// Redirect if already logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST['search_btn'])) {
  $doctor = trim($_POST['doctor']);
  $department = trim($_POST['department']) ;
  $datestr = trim($_POST['date']);
  $message = '';

  if ($department == '' || $doctor == '' || $datestr == '') {
    $message = 'Please fill in all boxes';
  }

  // Validate name
  if ($message == '') {
    $name_parts = explode(' ', $doctor);

    if (count($name_parts) != 2) {
      $message = 'Incorrect first and last name';
    } else {
      $first_name = trim($name_parts[0]);
      $last_name = trim($name_parts[1]);
    }
  }

  // Validate time
  if ($message == '') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $datestr);

    if ($date_obj === false || $date_obj->format('Y-m-d') !== $datestr) {
      $message = 'Enter a valid date';
    }
  }

  // Main logic
  if ($message == '') {
    $sql = "SELECT a.start, a.end 
          FROM appointment a
          JOIN doctor d ON d.id = a.doctor_id
          JOIN department dept on dept.id = d.department_id
          WHERE 
            d.first_name = '$first_name' AND 
            d.last_name = '$last_name' AND
            dept.name = '$department' AND
            a.start BETWEEN '$datestr' AND DATE_ADD('$datestr', INTERVAL 24 HOUR)
          ORDER BY a.start";
    $result = $conn->query($sql);
    $aptmts = $result->fetch_all(MYSQLI_ASSOC);

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
        "doctor"=> $doctor,
        "start" => $curr->format("H:i"),
        "end" => $end->format("H:i")
      ]);
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
  <!-- <?php if ($message != "") {
    echo "<p>$message<p>";
  } ?> -->

  <h1 class="header">Patient Portal - Query for Available Doctors</h1>

  <div class="form_container">
    <!-- Schedule form -->
    <form class="form" method="post">
      <div class="form_body">
        <h2 class="form_header">Schedule an Appointment</h2>

        <div class="form_field_container">
          <label for="department">Department:</label>
          <input class="form_field" type="text" id="department" name="department" />

          <label for="doctor">Doctor:</label>
          <input class="form_field" type="text" id="doctor" name="doctor" />

          <label for="datetime">Date:</label>
          <input class="form_field" type="date" id="date" name="date" />

          <button class="form_button" type="submit" name="search_btn">
            View Available Times
          </button>
        </div>
      </div>
    </form>
  </div>

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
  <!-- </table>

  <table class="table">
    <tr>
      <th>Doctor</th>
      <th>Times</th>

    </tr>
    <?php foreach ($availableTimes as $slot): ?>
      <tr>
        <td><?= $slot["doctor"] ?></td>
        <td><?= $slot["start"] ?> - <?= $slot["end"] ?>
        <td></td>
      </tr>
    <?php endforeach; ?>
  </table> -->
</body>

</html>