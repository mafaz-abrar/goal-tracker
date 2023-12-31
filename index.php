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
  <link rel="shortcut icon" type="image/x-icon" href="icon.ico" />
  <title>Goal Tracker</title>
</head>

<body>
  <h1>Goal Tracker</h1>

  <p class='links'>
    <a href='./index.php'>Home</a>
    <a href='./app/entries/entries.php'>Entries</a>
    <a href='./app/goals/goals.php'>Goals</a>
    <a href='./app/activities/activities.php'>Activities</a>
  </p>

  <p class='controls'>
    <a href='./app/entries/entry.php?mode=add'> + Add Entry</a>
  </p>

  <form action="index.php" method="POST" class='control-form'>
    <p class='form_input_container'>
      <input type='date' name='filter_date' class='filter-input' <?php echo isset($_POST['filter_date']) ? "value=" . add_single_quotes($_POST['filter_date']) : '' ?> />
      <button>Filter By Date</button>
    </p>
  </form>

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

  function get_activities_data_for_this_week(string $date): array
  {
    $db_access = new db_access();
    $this_week_activities_data_sql =
      " SELECT
          sec_to_time(sum(time_to_sec(entries.hours_spent))) AS total_hours_for_date,
          entries.date,
          entries.activity_id,
          activities.activity_name,
          activities.goal_id,
          goals.goal_name
        FROM
          entries
          INNER JOIN activities ON activities.activity_id = entries.activity_id
          INNER JOIN goals ON goals.goal_id = activities.goal_id
        WHERE
          yearweek(entries.date, 1) = yearweek(" . add_single_quotes($date) . ", 1) 
        GROUP BY
          entries.date,
          entries.activity_id,
          activities.activity_name,
          activities.goal_id,
          goals.goal_name
        ORDER BY
          goals.goal_name,
          activities.activity_name
      ";
    $db_access->execute_query($this_week_activities_data_sql);

    $activities_data = array();
    while ($data = $db_access->get_next_row()) {
      $activities_data[$data['activity_id'] . ';' . $data['goal_name'] . ';' . $data['activity_name']][] = [
        'date' => $data['date'],
        'total_hours_for_date' => $data['total_hours_for_date']
      ];
    }

    // TODO: use $data['id'] . ';' . $data['name'] to get both name and id, then pass to tables
    // links to generate buttons.

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

  if (isset($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
  } else {
    $filter_date = date("Y-m-d");
  }

  $activities_data = get_activities_data_for_this_week($filter_date);

  $table = '<table>';

  $table .= '<thead>';
  $table .= '<tr>';
  $table .= '<th>Goal</th>';
  $table .= '<th>Activity</th>';
  $table .= '<th>Monday</th>';
  $table .= '<th>Tuesday</th>';
  $table .= '<th>Wednesday</th>';
  $table .= '<th>Thursday</th>';
  $table .= '<th>Friday</th>';
  $table .= '<th>Saturday</th>';
  $table .= '<th>Sunday</th>';
  $table .= '<th>Controls</th>';
  $table .= '</tr>';
  $table .= '</thead>';

  $table .= '<tbody>';

  foreach ($activities_data as $activity_id_and_name => $activity_data) {
    $activity_id_and_name_array = explode(';', $activity_id_and_name);
    $activity_id = $activity_id_and_name_array[0];
    $goal_name = $activity_id_and_name_array[1];
    $activity_name = $activity_id_and_name_array[2];

    $failure_row = $goal_name === 'Keep track of my failures';
    $days_with_hours_for_activity = get_day_and_hours_array_for_activity_data($activity_data);

    $table .= '<tr>';
    $table .= '<td>' . $goal_name . '</td>';
    $table .= '<td>' . $activity_name . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Monday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Monday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Tuesday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Tuesday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Wednesday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Wednesday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Thursday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Thursday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Friday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Friday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Saturday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Saturday'] . '</td>';
    $table .= (($failure_row && $days_with_hours_for_activity['Sunday'] !== '') ? "<td class='failure-cell'>" : '<td>') . $days_with_hours_for_activity['Sunday'] . '</td>';

    $href = './app/entries/entry.php?mode=add&activity_id=' . $activity_id;
    $table .= "<td><a class='control' href=" . add_single_quotes($href) . ">+Add</a></td>";

    $table .= '</tr>';
  }

  $table .= '</tbody>';

  $table .= '</table>';

  echo $table;

  ?>
</body>

</html>