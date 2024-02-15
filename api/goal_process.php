<?php
include_once('../framework/db_access.php');
include_once('./api_utils.php');

try {

  $db_access = new db_access();

  switch ($_GET['mode']) {
    case 'add':
      $goal = new goal($db_access);
      break;
    case 'edit':
    case 'delete':
      $goal = new goal($db_access, $_POST['goal_id']);
      break;
    default:
      exit('Unknown mode!');
  }

  if (isset($_POST['goal_name']))
    $goal->goal_name = addslashes($_POST['goal_name']);

  switch ($_GET['mode']) {
    case 'add':
      $goal->insert_new();
      break;
    case 'edit':
      $goal->update_existing();
      break;
    case 'delete':
      $goal->delete_existing();
      break;
    default:
      exit('Unknown mode!');
  }
} catch (Exception $err) {
  generate_json_response($err->getMessage());
}

generate_json_response('Success');
