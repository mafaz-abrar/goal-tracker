<?php
include_once(__DIR__ . '/../../framework/db_access.php');

$db_access = new db_access();

switch ($_GET['mode']) {
  case 'add':
    $activity = new activity($db_access);
    break;
  case 'edit':
  case 'delete':
    $activity = new activity($db_access, $_GET['activity_id']);
    break;
  default:
    exit('Unknown mode!');
}

if (isset($_POST['goal_id']))
  $activity->goal_id = $_POST['goal_id'];

if (isset($_POST['activity_name']))
  $activity->activity_name = $_POST['activity_name'];

switch ($_GET['mode']) {
  case 'add':
    $activity->insert_new();
    break;
  case 'edit':
    $activity->update_existing();
    break;
  case 'delete':
    $activity->delete_existing();
    break;
  default:
    exit('Unknown mode!');
}

header('Location: ' . 'activities.php');
exit();
