<?php
include_once(__DIR__ . '/../../framework/db_access.php');
include_once(__DIR__ . '/../../utils/sql_utils.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href="../../styles/styles.css" />
  <title>Goal Tracker</title>
</head>

<body>
  <h1>Entries</h1>

  <p class='links'>
    <a href='../../index.php'>Home</a>
    <a href='./entries.php'>Entries</a>
    <a href='../goals/goals.php'>Goals</a>
    <a href='../activities/activities.php'>Activities</a>
  </p>

  <p class='controls'><a href='./entry.php?mode=add'> + Add Entry</a></p>

  <?php
  $db_access = new db_access();

  $query =
    " SELECT
        entries.entry_id, 
        entries.date,
        goals.goal_name,
        activities.activity_name,
        entries.task_description,
        entries.hours_spent,
        entries.start_time,
        entries.end_time
      FROM 
        entries 
        INNER JOIN activities ON activities.activity_id = entries.activity_id 
        INNER JOIN goals ON goals.goal_id = activities.goal_id
      ORDER BY 
        entries.date DESC,
        goals.goal_name,
        activities.activity_name
  ";

  $db_access->execute_query($query);

  if ($db_access->has_rows()) {
    $table = '<table>';
    $table .= '<thead><tr>';

    $column_names = ['Day', 'Date', 'Goals', 'Activity', 'Description', 'Hours', 'Start', 'End', 'Controls'];
    foreach ($column_names as $column_name) {
      $table .= '<th>' . $column_name . '</th>';
    }
    $table .= '</tr></thead>';

    $table .= '<tbody>';
    while ($row = $db_access->get_next_row()) {
      $table .= '<tr>';
      $table .= '<td>' . date('l', strtotime($row['date'])) . '</td>';
      $table .= '<td>' . $row['date'] . '</td>';
      $table .= '<td>' . $row['goal_name'] . '</td>';
      $table .= '<td>' . $row['activity_name'] . '</td>';
      $table .= '<td>' . $row['task_description'] . '</td>';
      $table .= '<td>' . $row['hours_spent'] . '</td>';
      $table .= '<td>' . $row['start_time'] . '</td>';
      $table .= '<td>' . $row['end_time'] . '</td>';
      $table .= '<td>';

      $table .=
        "<a class='control' href='entry.php?mode=edit" .
        '&entry_id=' . $row['entry_id'] . "'" .
        '>Edit</a>';

      $table .=
        "<a class='control' onclick='return confirm(\"Are you sure?\")' href='entry_process.php?mode=delete" .
        '&entry_id=' . $row['entry_id'] . "'" .
        '>Delete</a>';

      $table .= '</td></tr>';
    }
    $table .= '</tbody></table>';

    echo $table;
  } else {
    echo "<p class='empty-result'>No entries to show.</p>";
  }


  ?>
</body>

</html>