<?php
include_once(__DIR__ . '/framework/db_access.php');

if ($_GET['mode'] === 'edit') {
  $db_access = new db_access();
  $goal = new goal($db_access, $_GET['goal_id']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href='./styles/styles.css' />
  <title>Goal</title>
</head>

<body>
  <h1><?php echo ($_GET['mode'] === 'add' ? 'Add New' : 'Edit'); ?> Goal</h1>

  <form action=<?php echo 'goal_process.php?mode=' . $_GET['mode'] ?> method='post'>

    <?php if ($_GET['mode'] === 'edit') : ?>
      <input type="hidden" name="goal_id" value=<?php echo add_single_quotes($_GET['goal_id']); ?> />
    <?php endif; ?>

    <p class="form_input_container">
      <label for='goal_name' id='goal_name'>Goal</label>
      <input name='goal_name' type='text' <?php echo $_GET['mode'] === 'edit' ? "value=" . add_single_quotes($goal->goal_name) : '' ?> />
    </p>

    <p class="controls">
      <button>Submit</button>
      <a href='./goals.php'>Cancel</a>
    </p>
  </form>
</body>

</html>