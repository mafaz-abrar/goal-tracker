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
  <link rel="shortcut icon" type="image/x-icon" href="../../icon.ico" />
  <title>Goal Tracker</title>
</head>

<body>
  <h1>Goals</h1>

  <p class='links'>
    <a href='../../index.php'>Home</a>
    <a href='../entries/entries.php'>Entries</a>
    <a href='./goals.php'>Goals</a>
    <a href='../activities/activities.php'>Activities</a>
  </p>

  <p class='controls'><a href='./goal.php?mode=add'> + Add Goal</a></p>

  <?php
  $db_access = new db_access();

  $query =
    " SELECT
       *
      FROM 
        goals
      ORDER BY
        goal_name
    ";

  $db_access->execute_query($query);

  if ($db_access->has_rows()) {
    $table = '<table>';
    $table .= '<thead><tr>';

    $column_names = ['Goal', 'Controls'];
    foreach ($column_names as $column_name) {
      $table .= '<th>' . $column_name . '</th>';
    }
    $table .= '</tr></thead>';

    $table .= '<tbody>';
    while ($row = $db_access->get_next_row()) {
      $table .= '<tr>';
      $table .= '<td>' . $row['goal_name'] . '</td>';
      $table .= '<td>';

      $table .=
        "<a class='control' href='goal.php?mode=edit" .
        '&goal_id=' . $row['goal_id'] . "'" .
        '>Edit</a>';

      $table .=
        "<a class='control' onclick='return confirm(\"Are you sure?\")' href='goal_process.php?mode=delete" .
        '&goal_id=' . $row['goal_id'] . "'" .
        '>Delete</a>';

      $table .= '</td></tr>';
    }
    $table .= '</tbody></table>';

    echo $table;
  } else {
    echo "<p class='empty-result'>No goals to show.</p>";
  }
  ?>
</body>

</html>