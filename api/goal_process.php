<?php
include_once(__DIR__ . '/../../framework/db_access.php');

$db_access = new db_access();

switch ($_GET['mode']) {
  case 'add':
    $goal = new goal($db_access);
    break;
  case 'edit':
  case 'delete':
    $goal = new goal($db_access, $_GET['goal_id']);
    break;
  default:
    exit('Unknown mode!');
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
    exit('Unknown mode!');
}

header('Location: ' . 'goals.php');
exit();
