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

// Redirect if already logged in
if (isset($_SESSION["patient_id"])) {
  header('Location: dashboard.php');
  exit();
}
if (isset($_SESSION["doctor_id"])) {
  header('Location: dashboard.php');
  exit();
}

// Handle patient log in
if (isset($_POST["patient_login_btn"])) {
  $email = $_POST["email"] ?? '';
  $password = $_POST["password"] ?? '';

  // Basic form validation
  if ($email == '' || $password == '') {
    $message = 'EMAIL and password fields need to be filled out';
  } else {
    // Check if the user exists
    $sql = "SELECT id FROM patient WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      $_SESSION["patient_id"] = $row["id"];
      header('Location: dashboard.php');
      exit();
    } else {
      $message = 'Incorrect username or password';
    }
  }
}
// Handle caregiver
if (isset($_POST["caregiver_login_btn"])) {
  $email = $_POST["email"] ?? '';
  $password = $_POST["password"] ?? '';

  // Basic form validation
  if ($email == '' || $password == '') {
    $message = 'EMAIL and password fields need to be filled out';
  } else {
    // Check if the user exists
    $sql = "SELECT id FROM doctor WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      $_SESSION["doctor_id"] = $row["id"];
      header('Location: dashboard.php');
      exit();
    } else {
      $message = 'Incorrect username or password';
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
    <title>Login</title>
    <link rel="stylesheet" href="login_styles.css" />
  </head>

  <body>
    <h1 class="header">Health Access - Log In</h1>

    <!-- Error Message -->
    <?php if ($message != "") {
      echo "<div class=\"message-container\">
        <p class=\"error-message\">$message</p>
      </div>";
    } ?>
    <!-- <div class="message-container">
      <p class="error-message">$message</p>
    </div> -->

    <div class="form_container">
      <!-- Patient Form -->
      <form class="form" method="post">
        <div class="form_body">
          <h2 class="form_header">Patient Login</h2>

          <div class="form_field_container">
            <label for="patient-email">Email:</label>
            <input
              class="form_field"
              type="text"
              id="patient-email"
              name="email"
            />

            <label for="patient-password">Password:</label>
            <input
              class="form_field"
              type="text"
              id="patient-password"
              name="password"
            />

            <button class="form_button" type="submit" name="patient_login_btn">
              Log In
            </button>
          </div>
        </div>
      </form>

      <!-- Caregiver Form -->
      <form class="form" method="post">
        <div class="form_body">
          <h2 class="form_header">Caregiver Login</h2>

          <div class="form_field_container">
            <label for="caregiver-email">Email:</label>
            <input
              class="form_field"
              type="text"
              id="caregiver-email"
              name="email"
            />

            <label for="caregiver-password">Password:</label>
            <input
              class="form_field"
              type="text"
              id="caregiver-password"
              name="password"
            />

            <button
              class="form_button"
              type="submit"
              name="caregiver_login_btn"
            >
              Log In
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="info_box">
      <h2 class="info_box_header">Contact</h2>

      <div class="info_box_body">
        <div class="info_box_body_left">
          <p><strong>Address</strong></p>
          <p>1000 Billings Avenue</p>
          <p>Rochester, NY 14627</p>
        </div>
        <div class="info_box_body_right">
          <p><strong>General inquiries:</strong> (555) 742-8301</p>
          <p><strong>Health line:</strong> (555) 742-8307</p>
          <p><strong>Emergencies:</strong> 911</p>
        </div>
      </div>
    </div>
  </body>
</html>
