<?php
include_once('../framework/db_access.php');
include_once('./api_utils.php');

try {
  $db_access = new db_access();

  switch ($_GET['mode']) {
    case 'add':
      $activity = new activity($db_access);
      break;
    case 'edit':
    case 'delete':
      $activity = new activity($db_access, $_POST['activity_id']);
      break;
    default:
      exit('Unknown mode!');
  }

  if (isset($_POST['goal_id']))
    $activity->goal_id = $_POST['goal_id'];

  if (isset($_POST['activity_name']))
    $activity->activity_name = addslashes($_POST['activity_name']);

  if (isset($_POST['targeting'])) {
    $activity->targeting = $_POST['targeting'] === 'true';
  }
  if (isset($_POST['weighting'])) {
    $activity->weighting = $_POST['weighting'];
  }

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
} catch (Exception $err) {
  generate_json_response($e->getMessage());
}

generate_json_response('Success');
