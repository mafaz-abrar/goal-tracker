<?php
include_once(dirname(__FILE__) . "/framework/db_access.php");

$db_access = new db_access();
$errors = '';

$create_entries_table_sql =
  "CREATE TABLE IF NOT EXISTS entries (
    entry_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL, 
    goal_id INT NOT NULL,
    activity_id INT NOT NULL,
    task_description TEXT NOT NULL,
    hours_spent INT NOT NULL,
    start_time TIME,
    end_time TIME
  )";

$create_goals_table_sql =
  "CREATE TABLE IF NOT EXISTS goals (
    goal_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goal_name TEXT NOT NULL
  )
  ";

$create_activities_table_sql =
  "CREATE TABLE IF NOT EXISTS activities (
    activity_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goal_id INT NOT NULL,
    activity_name TEXT NOT NULL
  )";

if (!$db_access->execute_query($create_entries_table_sql)) {
  $errors .= $db_access->get_error() . "\n";
}

if (!$db_access->execute_query($create_goals_table_sql)) {
  $errors .= $db_access->get_error() . "\n";
};

if (!$db_access->execute_query($create_activities_table_sql)) {
  $errors .= $db_access->get_error() . "\n";
}

$url = 'index.php';

$tables_created_successfully = $errors == '';
$url .= '?tables_created=' . $tables_created_successfully;

if (!$tables_created_successfully) {
  $url .= '?errors=' . $errors;
}

header('Location: ' . $url);
exit();
