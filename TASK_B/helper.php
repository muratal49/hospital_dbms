<?php
function getAvailableSlots($conn, $department, $datestr): array
{
  $departmentAvailability = [];

  // Get appointments from doctors in the department
  $sql = "SELECT 
            doc.id as doctor_id, 
            CONCAT(doc.first_name, ' ', doc.last_name) as doctor_name,
            apt.start, apt.end
          FROM doctor doc
          JOIN department dpt on doc.department_id = dpt.id
          LEFT JOIN appointment apt on doc.id = apt.doctor_id AND
            apt.start >= '$datestr 09:00:00' AND 
            apt.end <= '$datestr 20:00:00'
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
        $availableTimes[] = [
          "start" => $curr->format("H:i"),
          "end" => $aptmt_start->format("H:i")
        ];
      }

      // Update next interval start time
      if ($aptmt_end > $curr) {
        $curr = $aptmt_end;
      }
    }

    // Check if there is remaining time after last appointment
    if ($curr < $end) {
      $availableTimes[] = [
        "start" => $curr->format("H:i"),
        "end" => $end->format("H:i")
      ];
    }

    if (!empty($availableTimes)) {
      $departmentAvailability[] = [
        "id" => $doc_id,
        "doctor" => $doc_data["name"],
        "times" => $availableTimes
      ];
    }
  }

  return $departmentAvailability;
}

?>