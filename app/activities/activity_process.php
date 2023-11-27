<?php
include_once(__DIR__ . '/framework/db_access.php');
include_once(__DIR__ . '/sql_utils.php');

$db_access = new db_access('test');
$activity = new activity($db_access);

if (isset($_POST['goal_id']))
  $activity->goal_id = add_single_quotes($_POST['goal_id']);

if (isset($_POST['activity_name']))
  $activity->activity_name = add_single_quotes($_POST['activity_name']);

$activity->insert_new();

header('Location: index.php');
exit();
