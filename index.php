<?php
include_once(__DIR__ . '/framework/db_access.php');
include_once(__DIR__ . '/utils/sql_utils.php');
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
  <h1>Goal Tracker</h1>

  <?php
  if (isset($_GET['tables_created'])) {
    if ($_GET['tables_created']) {
      echo "<p class='success'>Tables created successfully.</p>";
    } else {
      echo "<p class='errors'>" . $_GET['errors'] . '</p>';
    }
  }
  ?>

  <p class='links'>
    <a href='./index.php'>Home</a>
    <a href='./app/entries/entries.php'>Entries</a>
    <a href='./app/goals/goals.php'>Goals</a>
    <a href='./app/activities/activities.php'>Activities</a>
  </p>

  <p class='controls'>
    <a href='./setup_tables.php'>Setup Tables</a>
  </p>


  <?php

  function get_total_hours_logged_for_activity_for_day($activity_id, $day_name)
  {
    $day_date = date('Y-m-d', strtotime($day_name));

    $db_access = new db_access();
    $sql =
      " SELECT
          sec_to_time(sum(time_to_sec(entries.hours_spent))) AS hours_spent
        FROM
          entries
        WHERE
          entries.date = " .  add_single_quotes($day_date) .
      "   AND entries.activity_id = " . $activity_id .
      ";";

    $db_access->execute_query($sql);

    return $db_access->get_next_row()['hours_spent'];
  }

  $this_week_activities = new db_access();
  $this_week_activities_sql =
    " SELECT
        entries.activity_id,
        activities.activity_name
      FROM
        entries
        INNER JOIN activities ON activities.activity_id = entries.activity_id
      WHERE
        yearweek(entries.date, 1) = yearweek(curdate(), 1)
      GROUP BY
        activities.activity_name
  ";
  $this_week_activities->execute_query($this_week_activities_sql);

  // echo sql_to_table($db_access);

  $table = '<table>';

  $table .= '<thead>';
  $table .= '<tr>';
  $table .= '<th>Activity</th>';
  $table .= '<th>Monday</th>';
  $table .= '<th>Tuesday</th>';
  $table .= '<th>Wednesday</th>';
  $table .= '<th>Thursday</th>';
  $table .= '<th>Friday</th>';
  $table .= '<th>Saturday</th>';
  $table .= '<th>Sunday</th>';
  $table .= '</tr>';
  $table .= '</thead>';


  $table .= '<tbody>';

  while ($row = $this_week_activities->get_next_row()) {
    $table .= '<tr>';
    $table .= '<td>' . $row['activity_name'] . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Monday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Tuesday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Wednesday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Thursday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Friday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Saturday') . '</td>';
    $table .= '<td>' . get_total_hours_logged_for_activity_for_day($row['activity_id'], 'Sunday') . '</td>';
    $table .= '</tr>';
  }

  $table .= '</tbody>';

  $table .= '</table>';

  echo $table;

  ?>
</body>

</html>