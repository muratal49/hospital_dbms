<?php
session_start();

// Logout now requires a POST request to avoid CSRF-prone GET logouts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit();
}

// Dispatch based on logged-in role
if (isset($_SESSION['patient_id'])) {
  header('Location: patient_view_appointments.php');
  exit();
}

if (isset($_SESSION['doctor_id'])) {
  // If you want to send doctors to a custom dashboard, change this target
  header('Location: schedule.php');
  exit();
}

// Not logged in - send to login
header('Location: login.php');
exit();
