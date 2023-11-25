<?php
include_once(dirname(__FILE__) . '/framework/db_access.php');
include_once(dirname(__FILE__) . '/sql_to_table.php');

function process_add()
{
  $db_access = new db_access('test');
  $entry = new entry();

  if (isset($_POST['goal_id']))
    $entry->goal_id = $_POST['goal_id'];

  if (isset($_POST['activity_id']))
    $entry->activity_id = $_POST['activity_id'];

  if (isset($_POST['date']))
    $entry->date = add_single_quotes($_POST['date']);

  if (isset($_POST['task_description']))
    $entry->task_description = add_single_quotes($_POST['task_description']);

  if (isset($_POST['hours']))
    $entry->hours = $_POST['hours'];

  if (isset($_POST['start_time']) && $_POST['start_time'] != '') {
    $entry->start_time = $_POST['start_time'];
  } else {
    $entry->start_time = add_single_quotes("null");
  }

  if (isset($_POST['end_time']) && $_POST['end_time'] != '') {
    $entry->end_time = $_POST['end_time'];
  } else {
    $entry->end_time = add_single_quotes("null");
  }

  $db_access->save_new_object($entry);

  header('Location: index.php');
  exit();
}

if (isset($_POST['goal_id'])) {
  process_add();
};

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
  <link rel='stylesheet' href='./styles.css' />
  <title>add_entry.php</title>
</head>

<body>
  <h1>Add New Entry</h1>


  <form action='add_entry.php' method='post'>

    <p><label for='goal_id' id='goal_id'> Goal
        <select name='goal_id'>
          <?php
          $db_access = new db_access();

          $select_query =
            "SELECT goal_id, goal_name FROM goals";

          $result = $db_access->execute_query($select_query);

          if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<option value='" . $row['goal_id'] . "'>";
              echo $row['goal_name'];
              echo "</option>";
            }
          }
          ?>

        </select>
      </label></p>

    <p><label for='activity_id' id='activity_id'> Activity
        <input name='activity_id' type='text' />
      </label></p>

    <p><label for='date' id='date'> Date
        <input name='date' type='date' />
      </label></p>

    <p><label for='task_description' id='task_description'> Task Description
        <input name='task_description' type='text' />
      </label></p>

    <p><label for='hours' id='hours'> Hours
        <input name='hours' type='text' />
      </label></p>

    <p><label for='start_time' id='start_time'> Start Time
        <input name='start_time' type='text' />
      </label></p>

    <p><label for='end_time' id='end_time'> End Time
        <input name='end_time' type='text' />
      </label></p>

    <button>Submit</button>

  </form>

  <a href='./index.php'>Cancel</a>
</body>

</html>