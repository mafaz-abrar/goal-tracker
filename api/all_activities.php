<?php
include_once('./api_utils.php');
include_once('../framework/db_access.php');

class simple_activity
{
  public int $activity_id;
  public int $goal_id;
  public string $activity_name;
  public bool $targeting;
  public int $weighting;
}

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

    $entries[] = $activity;
  }

  return $entries;
}

generate_json_response(get_all_activities());
