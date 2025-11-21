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

// Redirect if not logged in
if (!isset($_SESSION["patient_id"])) {
  header('Location: login.php');
  exit();
}

if (isset($_POST["update_info_btn"])) {
  $phone = $_POST["phone"] ?? '';
  $email = $_POST["email"] ?? '';
  $home_address = $_POST["home_address"] ?? '';
  $pharmacy_address = $_POST["pharmacy_address"] ?? '';
  $id = $_SESSION["patient_id"];

  if ($phone == '' || $email == '' || $home_address == '' || $pharmacy_address == '') {
    $message = 'All fields must be filled out!';
  }

  $sql = "SELECT dob, first_name, last_name, email FROM patient WHERE id = '$id'";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();
  
  $session_dob = $row['dob'];
  $session_first_name = $row['first_name'];
  $session_last_name = $row['last_name'];
  $session_email = $row['email'];

  $sql2 = "SELECT * FROM patient WHERE id != '$id' AND dob = '$session_dob' AND first_name = '$session_first_name' AND last_name = '$session_last_name' AND email = '$email'";
  $result2 = $conn->query($sql2);

  if ($result2->num_rows > 0) {
    $message = 'Please use a different email address.';
  }

  if ($message != '') {
  $update = $conn->prepare("UPDATE patient SET phone = ?, email = ?, home_address = ?, pharmacy_address = ? WHERE id = ?");

  $update->bind_param("ssssi", $phone, $email, $home_address, $pharmacy_address, $SESSION["patient_id"]);

  $update->execute();
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

		<div class="form_container">
			<form class="/submit" method="post">
				<div class="form_body">
     					<h2 class="form_header">Patient update information</h2>

					<div class="form_field_container">

						<label for="phone">Phone number:</label>
						<input type="tel" id="phone" name="phone"><br><br>

						<label for="email">Email address:</label>
						<input type="email" id="email" name="email"><br><br>

						<label for="home_address">Home address:</label>
						<input type="text" id="home_address" name="home_address"><br><br>

						<label for="pharmacy_address">Pharmacy address:</label>
						<input type="text" id="pharmacy_address" name="pharmacy_address"><br><br>

						<button type="submit" name="update_info_btn">Update</button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>