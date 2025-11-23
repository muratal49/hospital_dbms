<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

if (!isset($_SESSION["doctor_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST['go_back_btn'])) {
  header('Location: caregiver_dashboard.php');
  exit();
}

$message = "";
if (isset($_POST["view_patient_information_btn"])) {
  $email = trim($_POST["email"] ?? '');

  // Basic form validation
  if ($email === '') {
    $message = 'Email must be filled out';
  } else {
    // Check for email matches
    $stmt = $conn->prepare(
      "SELECT id FROM patient WHERE email = ? LIMIT 1"
    );

    if ($stmt === false) {
      $message = 'Database error: failed to prepare statement';
    } else {
      $stmt->bind_param('s', $email);
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

  <!-- Error Message -->
  <?php if ($message != "") {
    echo "<div class=\"message-container\">
        <p class=\"error-message\">$message</p>
      </div>";
  } ?>

  <div class="container">
    <form method="post" action="view_patient_information.php">
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
        <button class="button" type="submit" name="view_patient_information_btn">Retrieve Patient Data</button>
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