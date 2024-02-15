<?php
include('./api_utils.php');
include('../framework/db_access.php');

try {

  $db_access = new db_access();

  switch ($_GET['mode']) {
    case 'add':
      $entry = new entry($db_access);
      break;
    case 'edit':
    case 'delete':
      $entry = new entry($db_access, $_POST['entry_id']);
      break;
    default:
      throw new Exception('Unknown mode!');
  }

  if (isset($_POST['activity_id'])) {
    $entry->activity_id = $_POST['activity_id'];
  }

  if (isset($_POST['date']))
    $entry->date = $_POST['date'];

  if (isset($_POST['task_description']))
    $entry->task_description = $_POST['task_description'];

  if (isset($_POST['hours']))
    $entry->hours_spent = $_POST['hours'];

  if (isset($_POST['start_time']) && $_POST['start_time'] != '') {
    $entry->start_time = $_POST['start_time'];
  } else {
    $entry->start_time = null;
  }

  if (isset($_POST['end_time']) && $_POST['end_time'] != '') {
    $entry->end_time = $_POST['end_time'];
  } else {
    $entry->end_time = null;
  }

  switch ($_GET['mode']) {
    case 'add':

      $entry->insert_new();

      break;
    case 'edit':
      $entry->update_existing();
      break;
    case 'delete':
      $entry->delete_existing();
      break;
    default:
      throw new Exception('Unknown mode!');
  }
} catch (Exception $err) {
  generate_json_response($err->getMessage());
  exit();
}

generate_json_response('Success!');
