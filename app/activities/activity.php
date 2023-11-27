<?php

if (isset($_POST['submitted'])) {
  process_add();
};

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href='./styles/styles.css' />
  <title>Add Activity</title>
</head>

<body>
  <h1>Add New Activity</h1>

  <form action='add_activity.php' method='post'>

    <input type="hidden" name="submitted" value="true" />

    <p class=" form_input_container">
      <label for='goal_id' id='goal_id'>Goal</label>
      <select name='goal_id'>
        <?php
        $db_access = new db_access();

        $select_query =
          "SELECT goal_id, goal_name FROM goals";

        $db_access->execute_query($select_query);

        if ($db_access->query_success() && $db_access->has_rows()) {
          while ($row = $db_access->get_next_row()) {
            echo "<option value='" . $row['goal_id'] . "'>";
            echo $row['goal_name'];
            echo "</option>";
          }
        }
        ?>
      </select>
    </p>

    <p class="form_input_container">
      <label for='activity_name' id='activity_name'>Activity</label>
      <input name='activity_name' type='text' />
    </p>

    <p class="controls">
      <button>Submit</button>
      <a href='./index.php'>Cancel</a>
    </p>
  </form>
</body>

</html>