<?php
include_once('../../framework/db_access.php');
include_once('../../framework/sql_utils.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href="./styles/styles.css" />
  <title>Goal Tracker</title>
</head>

<body>
  <h1>Entries</h1>

  <p class='controls'>
    <a href='./index.php'>Entries</a>
    <a href='./goals.php'>Goals</a>
    <a href='./activities.php'>Activities</a>
  </p>

  <p class='controls'>
    <a href='setup_tables.php'>Create Tables</a>
  </p>

  <p class='controls'><a href='./entry.php?mode=add'> + Add Entry</a></p>

  <?php

  if (isset($_GET['tables_created']) && $_GET['tables_created']) {
    echo "<p class='success'>Tables created successfully.</p>";
  }

  if (isset($_GET['errors'])) {
    echo "<p class='error'>Error creating tables: " . $_GET['errors'] . "</p>";
  }

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
        INNER JOIN goals ON goals.goal_id = entries.goal_id
        INNER JOIN activities ON activities.activity_id = entries.activity_id 
      ORDER BY 
        entries.date DESC
  ";

  $db_access->execute_query($query);


  $table = '<table>';
  $table .= '<thead><tr>';

  $column_names = ['Date', 'Goals', 'Activity', 'Description', 'Hours', 'Start', 'End', 'Controls'];
  foreach ($column_names as $column_name) {
    $table .= '<th>' . $column_name . '</th>';
  }
  $table .= '</tr></thead>';

  $table .= '<tbody>';
  while ($row = $db_access->get_next_row()) {
    $table .= '<tr>';
    $table .= '<td>' . $row['date'] . '</td>';
    $table .= '<td>' . $row['goal_name'] . '</td>';
    $table .= '<td>' . $row['activity_name'] . '</td>';
    $table .= '<td>' . $row['task_description'] . '</td>';
    $table .= '<td>' . $row['hours_spent'] . '</td>';
    $table .= '<td>' . $row['start_time'] . '</td>';
    $table .= '<td>' . $row['end_time'] . '</td>';
    $table .= '<td>';

    $table .=
      "<a href='entry.php?mode=edit" .
      '&entry_id=' . $row['entry_id'] . "'" .
      '>Edit</a>';

    $table .=
      "<a href='entry.php?mode=delete" .
      '&entry_id=' . $row['entry_id'] . "'" .
      '>Delete</a>';

    $table .= '</tr>';
  }
  $table .= '</tbody></table>';

  echo $table;
  ?>
</body>

</html>