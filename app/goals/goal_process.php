<?php
include_once(dirname(__FILE__) . '/framework/db_access.php');
include_once(dirname(__FILE__) . '/framework/sql_utils.php');


$db_access = new db_access('test');

switch ($_GET['mode']) {
  case 'add':
    $goal = new goal($db_access);
    break;
  case 'edit':
  case 'delete':
    $goal = new goal($db_access, $_POST['goal_id']);
    break;
  default:
    echo 'Fail bitch';
}

if (isset($_POST['goal_name']))
  $goal->goal_name = $_POST['goal_name'];

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
    echo 'Fail bitch';
}

header('Location: goals.php');
exit();
