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
            doc.email as doctor_email,
            CONCAT(doc.first_name, ' ', doc.last_name) as doctor_name,
            apt.start, apt.end
          FROM doctor doc
          JOIN department dpt on doc.department_id = dpt.id
          LEFT JOIN appointment apt on doc.id = apt.doctor_id
            AND apt.start BETWEEN '$datestr' AND DATE_ADD('$datestr', INTERVAL 24 HOUR)
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
        'email' => $row['doctor_email'],
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
        array_push($availableTimes, [
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
        "start" => $curr->format("H:i"),
        "end" => $end->format("H:i")
      ]);
    }

    if (!empty($availableTimes)) {
      $departmentAvailability[] = [
        "doctor" => $doc_data["name"],
        "email" => $doc_data["email"],
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

  // Query doctors in department
  if ($message == '') {
    $availableSlots = getAvailableSlots($conn, $department, $datestr);

    if (count($availableSlots) == 0) {
      $message = 'No available appointments for this department on this date';
    } else {
      $departmentAvailability = $availableSlots;
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
    echo "<p>$message</p>";
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
      <th>Email</th>
      <th>Times</th>
    </tr>
    <?php foreach ($departmentAvailability as $availability): ?>
      <tr>
        <td><?= $availability["doctor"] ?></td>
        <td><?= $availability["email"]?></td>
        <td>
          <?php foreach ($availability["times"] as $slot): ?>
            <?= $slot["start"] ?> - <?= $slot["end"] ?><br>
          <?php endforeach; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>