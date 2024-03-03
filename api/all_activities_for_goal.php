<?php
include_once('../framework/db_access.php');
include_once('./api_utils.php');
include_once('./data_structures.php');

function get_all_activities_for_goal(int $goal_id): array
{
  $sql =
    " SELECT
      *
    FROM
      activities
    WHERE
      goal_id = " . $goal_id . "
  ";

  $db_access = new db_access();
  $db_access->execute_query($sql);

  $data = array();
  while ($row = $db_access->get_next_row()) {
    $activity = new simple_activity();
    $activity->activity_id = $row['activity_id'];
    $activity->goal_id = $row['goal_id'];
    $activity->activity_name = $row['activity_name'];
    $activity->targeting = $row['targeting'];
    $activity->weighting = $row['weighting'];
    $activity->target = $row['target'] / 60;

    $data[] = $activity;
  }

  return $data;
}

try {
  $goal_id = $_GET['goal_id'];
  generate_json_response(get_all_activities_for_goal($goal_id));
} catch (Exception $e) {
  generate_json_response('Failed with error: ' . $e->getMessage());
}
