<?php
include_once(__DIR__ . '/../../framework/db_access.php');

$db_access = new db_access();

$goals = array();

$goals_select_query =
  "SELECT * FROM goals";

$db_access->execute_query($goals_select_query);

while ($row = $db_access->get_next_row()) {
  $goals[] = $row;
}

if ($_GET['mode'] === 'edit') {
  $db_access = new db_access();
  $activity = new activity($db_access, $_GET['activity_id']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href='../../styles/styles.css' />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $(document).ready(() => {
      let goals = <?php echo json_encode($goals); ?>;

      let goal_dropdown = $("#goal_dropdown");

      goals.forEach((goal) => {
        goal_dropdown.append(new Option(goal.goal_name, goal.goal_id));
      });

      <?php
      if ($_GET['mode'] === 'edit') {
        echo "$('#goal_dropdown option[value=" . $entry->goal_id .
          "]').attr('selected', 'selected');";
      }
      ?>
    });
  </script>

  <title>Activity</title>
</head>

<body>
  <h1><?php echo ($_GET['mode'] === 'add' ? 'Add New' : 'Edit'); ?> Activity</h1>

  <form action=<?php echo 'activity_process.php?mode=' . $_GET['mode'] .
                  ($_GET['mode'] === 'edit' ? '&activity_id=' . $_GET['activity_id'] : '')
                ?> method='post'>

    <p class="form_input_container">
      <label for='goal_id' id='goal_id'>Goal</label>
      <select name='goal_id' id='goal_dropdown' required>
      </select>
    </p>

    <p class="form_input_container">
      <label for='activity_name' id='activity_name'>Activity</label>
      <input name='activity_name' type='text' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($activity->activity_name) : '' ?> required />
    </p>

    <p class="controls">
      <button>Submit</button>
      <a href='./activities.php'>Cancel</a>
    </p>
  </form>
</body>

</html>