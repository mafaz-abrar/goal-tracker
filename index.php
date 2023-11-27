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
</body>

</html>