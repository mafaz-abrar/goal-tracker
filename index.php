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

  <form action="index.php" method="POST" class='control-form'>
    <p class='form_input_container'>
      <input type='date' name='filter_date' class='filter-input' <?php echo isset($_POST['filter_date']) ? "value=" . add_single_quotes($_POST['filter_date']) : '' ?> />
      <button>Filter By Date</button>
    </p>
  </form>

  <?php

  function get_activities_as_array(): array
  {
    $db_access = new db_access();
    $activities_sql =
      " SELECT
          activities.activity_id,
          activities.activity_name,
          goals.goal_name
        FROM
          activities
          INNER JOIN goals ON goals.goal_id = activities.goal_id
        ORDER BY
          goals.goal_name,
          activities.activity_name
      ";
    $db_access->execute_query($activities_sql);

    $activities = array();
    while ($data = $db_access->get_next_row()) {
      $activities[$data['activity_id']] = $data['goal_name'] . ';' . $data['activity_name'];
    }

    return $activities;
  }

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
          entries.activity_id
        FROM
          entries
        WHERE
          yearweek(entries.date, 1) = yearweek(" . add_single_quotes($date) . ", 1) 
        GROUP BY
          entries.date,
          entries.activity_id
      ";
    $db_access->execute_query($this_week_activities_data_sql);

    $activities_data = array();
    while ($data = $db_access->get_next_row()) {
      $activities_data[$data['activity_id']][] = [
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
  $activities_as_array = get_activities_as_array();

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

  // loop over actvities array inseatd of activities_data arraay
  // match on id - use id as key for activties_data
  foreach ($activities_as_array as $activity_id => $goal_name_and_activity_name) {
    $goal_name_and_activity_name_array = explode(';', $goal_name_and_activity_name);
    $goal_name = $goal_name_and_activity_name_array[0];
    $activity_name = $goal_name_and_activity_name_array[1];

    if (isset($activities_data[$activity_id])) {
      $days_with_hours_for_activity = get_day_and_hours_array_for_activity_data($activities_data[$activity_id]);
    } else {
      $days_with_hours_for_activity = [
        'Monday' => '',
        'Tuesday' => '',
        'Wednesday' => '',
        'Thursday' => '',
        'Friday' => '',
        'Saturday' => '',
        'Sunday' => ''
      ];
    }

    $table .= '<tr>';
    $table .= '<td>' . $goal_name . '</td>';
    $table .= '<td>' . $activity_name . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Monday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Tuesday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Wednesday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Thursday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Friday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Saturday'] . '</td>';
    $table .= '<td>' . $days_with_hours_for_activity['Sunday'] . '</td>';

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