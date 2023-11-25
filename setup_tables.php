<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="author" content="Mafaz Abrar Jan Chowdhury" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>setup_tables.php</title>
</head>

<body>
  <a href='index.php'>Return</a>
</body>

</html>

<?php
include_once(dirname(__FILE__) . "/framework/db_access.php");

$db_access = new db_access("test");

$create_entries_table =
  "CREATE TABLE entries (
    entry_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL, 
    goal_id INT NOT NULL,
    activity_id INT NOT NULL,
    task_description VARCHAR(255) NOT NULL,
    hours_spent INT NOT NULL,
    start_time TIME,
    end_time TIME
  )";

$db_access->execute_query($create_entries_table);
