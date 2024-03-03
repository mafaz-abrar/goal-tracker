<?php
include_once('./api_utils.php');
include_once('../framework/db_access.php');
include_once('./data_structures.php');

function get_all_activities(): array
{
  $sql =
    " SELECT 
      *
    FROM
      activities
  ";

  $db_access = new db_access();
  $db_access->execute_query($sql);

  $entries = array();
  while ($row = $db_access->get_next_row()) {
    $activity = new simple_activity();
    $activity->activity_id = $row['activity_id'];
    $activity->goal_id = $row['goal_id'];
    $activity->activity_name = $row['activity_name'];
    $activity->targeting = $row['targeting'];
    $activity->weighting = $row['weighting'];
    $activity->target = $row['target'] / 60;

    $entries[] = $activity;
  }

  return $entries;
}

generate_json_response(get_all_activities());
