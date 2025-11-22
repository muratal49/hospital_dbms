<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

#caregiver check-in for patient first,last,and dob:
#Enter patient's first name, last name, and date of birth to check them in:

if (!isset($_SESSION["doctor_id"])) {
  header('Location: login.php');
  exit();
}

$message = "";
if (isset($_POST["caregiver_checkin_btn"])) {
  $email = trim($_POST["email"] ?? '');

  // Basic form validation
  if ($email === '') {
    $message = 'Email must be filled out';
  } else {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare(
      "SELECT id FROM patient WHERE email = ? LIMIT 1"
    );

    if ($stmt === false) {
      $message = 'Database error: failed to prepare statement';
    } else {
      $stmt->bind_param('s',$email);
      if (!$stmt->execute()) {
        $message = 'Database error: failed to execute query';
      } else {
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();


          // Redirect caregiver to 
          $_SESSION["checked_in_patient_id"] = $row["id"];
          header('Location: view_patient_history.php');
          exit();
        } else {
          $message = 'No matching patient found';
        }
      }
      $stmt->close();
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Caregiver Portal - Patient Check-in</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <h1>Caregiver Portal - Patient Check-in</h1>

  <div class="container">
    <form method="post" action="caregiver_patient_checkin.php">
      <!-- Patient Email -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Patient Email:</div>
        </div>

        <div class="form-group">
          <input type="email" name="email" placeholder="Enter patient email" required>
        </div>
      </div>

      <!-- Button -->
      <div class="button-container">
        <button class="button" type="submit" name="caregiver_checkin_btn">Retrieve Patient Data</button>
      </div>
    </form>
  </div>
</body>

</html>