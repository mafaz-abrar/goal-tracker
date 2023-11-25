<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="author" content="Mafaz Abrar Jan Chowdhury" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='stylesheet' href='./style.css' />
  <title>index.php</title>
</head>

<body>
  <h1>Entries</h1>

  <a href='setup_tables.php'>Create Tables</a>
  <a href='./add_entry.php'>Add Entry</a>

  <?php
  include_once(dirname(__FILE__) . '/framework/db_access.php');
  include_once('./sql_to_table.php');

  $db_access = new db_access("test");

  $query = "SELECT * FROM entries";

  $result = $db_access->execute_query($query);

  echo sql_to_table($result);
  ?>

</body>

</html>