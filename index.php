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
    <a href='./app/entries/entry.php?mode=add'> + Add Entry</a>
  </p>


  <?php

  function get_day_and_hours_array_for_activity_data(array $activity_data): array
  {
    $data = array();
    $data['Monday'] = '';
    $data['Tuesday'] = '';
    $data['Wednesday'] = '';
    $data['Thursday'] = '';
    $data['Friday'] = '';
    $data['Saturday'] = '';
    $data['Sunday'] = '';

    foreach ($activity_data as $row) {
      $day = date('l', strtotime($row['date']));
      $data[$day] = $row['total_hours_for_date'];
    }

    return $data;
  }

  function get_activities_data_for_this_week(): array
  {
    $db_access = new db_access();
    $this_week_activities_data_sql =
      " SELECT
          sec_to_time(sum(time_to_sec(entries.hours_spent))) AS total_hours_for_date,
          entries.date,
          activities.activity_name,
          activities.goal_id
        FROM
          entries
          INNER JOIN activities ON activities.activity_id = entries.activity_id
        WHERE
          yearweek(entries.date, 1) = yearweek(curdate(), 1)
        GROUP BY
          entries.date,
          activities.activity_name
        ORDER BY
          activities.goal_id
      ";
    $db_access->execute_query($this_week_activities_data_sql);

    $activities_data = array();
    while ($data = $db_access->get_next_row()) {
      $activities_data[$data['activity_name']][] = [
        'date' => $data['date'],
        'total_hours_for_date' => $data['total_hours_for_date']
      ];
    }

    // Structure of activities data:
    // [
    //   'name1' => [
    //     ['date' => 'XXXX-XX-XX', 'total_hours_for_date' => ''],
    //     ['date' => 'XXXX-XX-XX', 'total_hours_for_date' => ''],
    //   ],
    //   'name2' => [
    //     ['date' => 'XXXX-XX-XX', 'total_hours_for_date' => ''],
    //   ]
    // ]


    return $activities_data;
  }

  $activities_data = get_activities_data_for_this_week();

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

  foreach ($activities_data as $activity_name => $activity_data) {
    $days_with_hours_for_activity = get_day_and_hours_array_for_activity_data($activity_data);

    $table .= '<tr>';
    $table .= '<td>' . $activity_name . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Monday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Tuesday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Wednesday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Thursday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Friday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Saturday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Sunday'] . '</td>';
    $table .= '</tr>';
  }

  $table .= '</tbody>';

  $table .= '</table>';

  echo $table;

  ?>
</body>

</html>