<?php
include_once(__DIR__ . '/../../framework/db_access.php');
include_once(__DIR__ . '/../../utils/sql_utils.php');

$db_access = new db_access();

$goals = array();

$goals_select_query =
  "SELECT * FROM goals";

$db_access->execute_query($goals_select_query);

while ($row = $db_access->get_next_row()) {
  $goals[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href="../../styles/styles.css" />
  <link rel="shortcut icon" type="image/x-icon" href="../../icon.ico" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $(document).ready(() => {
      let goals = <?php echo json_encode($goals); ?>;

      let goal_dropdown = $("#filter_goal_dropdown");

      goals.forEach((goal) => {
        goal_dropdown.append(new Option(goal.goal_name, goal.goal_id));
      });

      <?php
      if (isset($_POST['filter_goal_id'])) {
        echo "$('#filter_goal_dropdown option[value=" . $_POST['filter_goal_id'] .
          "]').attr('selected', 'selected');";
      }
      ?>

    });
  </script>
  <title>Goal Tracker</title>
</head>

<body>
  <h1>Activities</h1>

  <p class='links'>
    <a href='../../index.php'>Home</a>
    <a href='../entries/entries.php'>Entries</a>
    <a href='../goals/goals.php'>Goals</a>
    <a href='./activities.php'>Activities</a>
  </p>

  <p class='controls'><a href='./activity.php?mode=add'> + Add Activity</a></p>

  <form action='activities.php' method='post'>
    <p class="form_input_container">
      <select name='filter_goal_id' id='filter_goal_dropdown'>
      </select>
      <button>Filter By Goal</button>
    </p>
  </form>

  <?php
  $db_access = new db_access();

  $query =
    " SELECT
        activities.activity_id,
        goals.goal_name,
        activities.activity_name
      FROM 
        activities
        INNER JOIN goals ON goals.goal_id = activities.goal_id
      ORDER BY
        goals.goal_name,
        activities.activity_name
    ";

  if (isset($_POST['filter_goal_id'])) {
    $query =
      "SELECT
        activities.activity_id,
        goals.goal_name,
        activities.activity_name
      FROM 
        activities
        INNER JOIN goals ON goals.goal_id = activities.goal_id
      WHERE
        activities.goal_id = " . $_POST['filter_goal_id']  . "
      ORDER BY
        goals.goal_name,
        activities.activity_name
    ";
  }

  $db_access->execute_query($query);

  if ($db_access->has_rows()) {
    $table = '<table>';
    $table .= '<thead><tr>';

    $column_names = ['Goal', 'Activity', 'Controls'];
    foreach ($column_names as $column_name) {
      $table .= '<th>' . $column_name . '</th>';
    }
    $table .= '</tr></thead>';

    $table .= '<tbody>';
    while ($row = $db_access->get_next_row()) {
      $table .= '<tr>';
      $table .= '<td>' . $row['goal_name'] . '</td>';
      $table .= '<td>' . $row['activity_name'] . '</td>';
      $table .= '<td>';

      $table .=
        "<a class='control' href='activity.php?mode=edit" .
        '&activity_id=' . $row['activity_id'] . "'" .
        '>Edit</a>';

      $table .=
        "<a class='control' onclick='return confirm(\"Are you sure?\")' href='activity_process.php?mode=delete" .
        '&activity_id=' . $row['activity_id'] . "'" .
        '>Delete</a>';

      $table .= '</td></tr>';
    }
    $table .= '</tbody></table>';

    echo $table;
  } else {
    echo "<p class='empty-result'>No activities to show.</p>";
  }
  ?>
</body>

</html>