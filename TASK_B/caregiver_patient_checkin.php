<?php
session_start();

require_once 'db_config.php';


$conn = getConnection();

#caregiver check-in for patient first,last,and dob:
#Enter patient's first name, last name, and date of birth to check them in:




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
      } 
      else 
        {
        $stmt->bind_param('ssss', $first_name, $last_name, $dob_norm, $email);
        if (!$stmt->execute()) {
          $message = 'Database error: failed to execute query';
        } else {
          $result = $stmt->get_result();
          if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Store patient id and basic info in session for later use
            $_SESSION["patient_id"] = $row["id"];
            $_SESSION["patient_info"] = [
              'id' => $row['id'],
              'first_name' => $row['first_name'],
              'last_name' => $row['last_name'],
              'dob' => $row['dob'],
              'phone' => $row['phone'] ?? null,
              'email' => $row['email'],
            ];

            // Retrieve past appointments and prescriptions for this patient
            $curr_datetime = date('Y-m-d H:i:s');
            $hist_stmt = $conn->prepare(
              "SELECT a.id, a.start, a.notes, CONCAT(d.first_name, ' ', d.last_name) AS doctor, CONCAT(p.name, ' ', p.dosage) AS prescription
               FROM appointment a
               JOIN doctor d ON a.doctor_id = d.id
               LEFT JOIN prescription p ON a.id = p.appointment_id
               WHERE a.patient_id = ? AND a.start < ?
               ORDER BY a.start DESC"
            );

            $patient_history = [];
            if ($hist_stmt !== false) {
              $hist_stmt->bind_param('is', $row['id'], $curr_datetime);
              if ($hist_stmt->execute()) {
                $hist_res = $hist_stmt->get_result();
                while ($h = $hist_res->fetch_assoc()) {
                  $aid = $h['id'];
                  if (!isset($patient_history[$aid])) {
                    $patient_history[$aid] = [
                      'id' => $aid,
                      'doctor' => $h['doctor'],
                      'start' => $h['start'],
                      'notes' => $h['notes'],
                      'prescriptions' => []
                    ];
                  }

                  if (!empty($h['prescription'])) {
                    $patient_history[$aid]['prescriptions'][] = $h['prescription'];
                  }
                }
              }
              $hist_stmt->close();
            }

            // Store history (re-index to numeric array) in session
            $_SESSION['patient_history'] = array_values($patient_history);

            // Redirect caregiver to caregiver-specific dashboard
            header('Location: caregiver_checkin_dashboard.php');
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
