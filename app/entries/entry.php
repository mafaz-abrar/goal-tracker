<?php
include_once(__DIR__ . '/../../framework/db_access.php');
include_once(__DIR__ . '/../../utils/sql_utils.php');

$db_access = new db_access();

$goals = array();
$activities = array();

$goals_select_query =
  "SELECT * FROM goals";

$activities_select_query =
  "SELECT * FROM activities";

$db_access->execute_query($goals_select_query);

while ($row = $db_access->get_next_row()) {
  $goals[] = $row;
}

$db_access->execute_query($activities_select_query);

while ($row = $db_access->get_next_row()) {
  $activities[] = $row;
}

if ($_GET['mode'] === 'edit') {
  $entry = new entry($db_access, $_GET['entry_id']);
}

if ($_GET['mode'] === 'add') {
  if (isset($_GET['activity_id'])) {
    $add_activity = new activity(new db_access(), $_GET['activity_id']);
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="author" content="Mafaz Abrar Jan Chowdhury" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href='../../styles/styles.css' />
  <link rel="shortcut icon" type="image/x-icon" href="../../icon.ico" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $(document).ready(() => {
      let goals = <?php echo json_encode($goals); ?>;
      let activities = <?php echo json_encode($activities); ?>;

      let goal_dropdown = $("#goal_dropdown");
      let activity_dropdown = $("#activity_dropdown");

      goals.forEach((goal) => {
        goal_dropdown.append(new Option(goal.goal_name, goal.goal_id));
      });

      <?php
      if ($_GET['mode'] === 'edit') {
        $activity = new activity(new db_access(), $entry->activity_id);
        echo 'goal_dropdown.val(' . add_single_quotes($activity->goal_id) . ');';
      }

      if ($_GET['mode'] === 'add' && isset($_GET['activity_id'])) {
        echo 'goal_dropdown.val(' . add_single_quotes($add_activity->goal_id) . ');';
      }
      ?>

      activities.forEach((activity) => {
        if (activity.goal_id === goal_dropdown.val()) {
          activity_dropdown.append(new Option(activity.activity_name, activity.activity_id));
        }
      })

      <?php
      if ($_GET['mode'] === 'edit') {
        echo 'activity_dropdown.val(' . add_single_quotes($entry->activity_id) . ');';
      }

      if ($_GET['mode'] === 'add' && isset($_GET['activity_id'])) {
        echo 'activity_dropdown.val(' . add_single_quotes($add_activity->get_id()) . ');';
      }
      ?>

      goal_dropdown.change(() => {
        activity_dropdown.empty();

        activities.forEach((activity) => {
          if (activity.goal_id === goal_dropdown.val()) {
            activity_dropdown.append(new Option(activity.activity_name, activity.activity_id));
          }
        })
      });

      <?php if ($_GET['mode'] === 'add') : ?>
        document.getElementById('date_input').valueAsDate = new Date();
      <?php endif; ?>
    });
  </script>

  <title>Entry</title>
</head>

<body>
  <h1><?php echo ($_GET['mode'] === 'add' ? 'Add New' : 'Edit'); ?> Entry</h1>

  <form action=<?php echo 'entry_process.php?mode=' . $_GET['mode'] .
                  ($_GET['mode'] === 'edit' ? '&entry_id=' . $_GET['entry_id'] : '')
                ?> method='post'>

    <p class="form_input_container">
      <label for='goal_id' id='goal_id'>Goal</label>
      <select name='goal_id' id='goal_dropdown' required>
      </select>
    </p>

    <p class="form_input_container">
      <label for='activity_id' id='activity_id'>Activity</label>
      <select name='activity_id' id='activity_dropdown' required>
      </select>
    </p>

    <p class="form_input_container">
      <label for='date' id='date'>Date</label>
      <input name='date' type='date' id='date_input' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($entry->date) : '' ?> required />
    </p>

    <p class="form_input_container">
      <label for='task_description' id='task_description'>Task Description</label>
      <textarea name='task_description' rows='4' cols='50' required><?php echo $_GET['mode'] === 'edit' ? $entry->task_description : '✅' ?></textarea>
    </p>

    <p class="form_input_container">
      <label for='hours' id='hours'>Hours Spent</label>
      <input name='hours' type='text' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($entry->hours_spent) : "value='00:05'" ?> required />
    </p>

    <p class="form_input_container">
      <label for='start_time' id='start_time'>Start Time</label>
      <input name='start_time' type='time' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($entry->start_time) : '' ?> />
    </p>

    <p class="form_input_container">
      <label for='end_time' id='end_time'>End Time</label>
      <input name='end_time' type='time' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($entry->end_time) : '' ?> />
    </p>

    <p class="controls">
      <button>Submit</button>
      <a href='./entries.php'>Cancel</a>
    </p>
  </form>


</body>

</html>