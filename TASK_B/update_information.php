<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

$message = "";

// Redirect if not logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

$this_id = $_SESSION["patient_id"];
if (isset($_POST["delete_account_btn"])) {
  $sql = "DELETE from patient WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $this_id);

  if ($stmt->execute()) {
    unset($_SESSION["patient_id"]);
    header('Location: login.php');
    exit();
  } else {
    $message = "Error deleting account: " . $conn->error;
  }
}

$id = $_SESSION["patient_id"];
$sql = "SELECT email, phone, pharmacy_address FROM patient WHERE id = '$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$session_email = $row['email'];
$session_phone = $row['phone'];
// $session_home_address = $row['home_address'];
$session_pharmacy_address = $row['pharmacy_address'];

if (isset($_POST["update_info_btn"])) {
  $phone = $_POST["phone"] ?? '';
  $email = $_POST["email"] ?? '';
  // $home_address = $_POST["home_address"] ?? '';
  $pharmacy_address = $_POST["pharmacy_address"] ?? '';

  if ($phone == '' || $email == '' || $pharmacy_address == '') {
    $message = 'All fields must be filled out!';
  }

  $sql = "SELECT dob, first_name, last_name FROM patient WHERE id = '$id'";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();

  $session_dob = $row['dob'];
  $session_first_name = $row['first_name'];
  $session_last_name = $row['last_name'];

  $sql2 = "SELECT * FROM patient WHERE id != '$id' AND email = '$email'";
  $result2 = $conn->query($sql2);

  if ($result2->num_rows > 0) {
    $message = 'Please use a different email address.';
  }

  if ($message == '') {
    $update = $conn->prepare("UPDATE patient SET phone = ?, email = ?, pharmacy_address = ? WHERE id = ?");

    $update->bind_param("sssi", $phone, $email, $pharmacy_address, $_SESSION["patient_id"]);

    if ($update->execute()) {
      header('Location: patient_dashboard.php');
      exit();
    } else {
      $message = 'Error updating information: ' . $conn->error;
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
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <h1 class="header">Patient Portal - Update My Information</h1>

  <!-- Error Message -->
  <?php if ($message != "") {
    echo "<div class=\"message-container\">
        <p class=\"error-message\">$message</p>
      </div>";
  } ?>

  <div class="container">
    <form method="post">
      <div class="form_body">
        <h2 class="form_header">Patient update information</h2>

        <div class="form_field_container">

          <label for="phone">Phone number:</label>
          <input type="tel" id="phone" name="phone" value="<?php echo $session_phone; ?>"><br><br>

          <label for="email">Email address:</label>
          <input type="email" id="email" name="email" value="<?php echo $session_email; ?>"><br><br>

          <!-----
            <label for="home_address">Home address:</label>
            <input type="text" id="home_address" name="home_address" value="<//?php echo $session_home_address; ?>"><br><br>
            ----->

          <label for="pharmacy_address">Pharmacy address:</label>
          <input type="text" id="pharmacy_address" name="pharmacy_address"
            value="<?php echo $session_pharmacy_address; ?>"><br><br>

          <button class="button" type="submit" name="update_info_btn">Update</button>
        </div>
      </div>
    </form>
    <form method="post">
      <button class="button" name="delete_account_btn" style="margin-top: 20px">DELETE MY ACCOUNT</button>
    </form>
  </div>
</body>

</html>