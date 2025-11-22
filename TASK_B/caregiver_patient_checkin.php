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
  $first_name = trim($_POST["first_name"] ?? '');
  $last_name = trim($_POST["last_name"] ?? '');
  $dob = trim($_POST["dob"] ?? '');
  $email = trim($_POST["email"] ?? '');

  // Basic form validation
  if ($first_name === '' || $last_name === '' || $dob === '' || $email === '') {
    $message = 'First name, Last name, Date of Birth, and Email fields need to be filled out';
  } else {
    // Optional: normalize DOB input (expecting YYYY-MM-DD). If not valid, return error.
    $dob_dt = DateTime::createFromFormat('Y-m-d', $dob);
    if ($dob_dt === false) {
      $message = 'Date of Birth must be in YYYY-MM-DD format';
    } else {
      $dob_norm = $dob_dt->format('Y-m-d');

      // Use prepared statements to prevent SQL injection
      $stmt = $conn->prepare(
        "SELECT id, first_name, last_name, dob, phone, email FROM patient WHERE first_name = ? AND last_name = ? AND dob = ? AND email = ? LIMIT 1"
      );

      if ($stmt === false) {
        $message = 'Database error: failed to prepare statement';
      } else {
        $stmt->bind_param('ssss', $first_name, $last_name, $dob_norm, $email);
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
      <!-- Patient First Name -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Patient First Name:</div>
        </div>

        <div class="form-group">
          <input type="text" name="first_name" required>
        </div>
      </div>

      <!-- Patient Last Name -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Patient Last Name:</div>
        </div>

        <div class="form-group">
          <input type="text" name="last_name" required>
        </div>
      </div>

      <!-- Patient Date of Birth -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Patient Date of Birth:</div>
        </div>

        <div class="form-group">
          <input type="date" name="dob" required>
        </div>
      </div>

      <!-- Patient Email -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Patient Email:</div>
        </div>

        <div class="form-group">
          <input type="email" name="email" placeholder="Enter patient email" required>
        </div>
      </div>

      <!-- Reference Date (optional) -->
      <div class="form-row">
        <div class="form-group">
          <div class="label">Reference Date (optional)</div>
        </div>

        <div class="form-group">
          <input type="date" name="reference_date" placeholder="Optional ( Default = ALL )">
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