<?php
include_once('../framework/db_access.php');
include_once('../api/api_utils.php');

class simple_goal
{
  public int $goal_id;
  public string $goal_name;
}

class simple_activity
{
  public int $activity_id;
  public int $goal_id;
  public string $activity_name;
  public bool $targeting;
  public int $weighting;
}

class goal_with_activities
{
  public simple_goal $goal;
  public array $activities;
}

function get_goals_with_activities()
{
  $db_access = new db_access();

  $sql =
    " SELECT
      goals.goal_name,
      activities.*
    FROM
      goals
      INNER JOIN activities ON activities.goal_id = goals.goal_id
  ";

  $db_access->execute_query($sql);

  $goals = array();

  while ($row = $db_access->get_next_row()) {
    $goals[$row['goal_id']]['goal_name'] = $row['goal_name'];

    $activity = new simple_activity();
    $activity->activity_id = $row['activity_id'];
    $activity->goal_id = $row['goal_id'];
    $activity->activity_name = $row['activity_name'];
    $activity->targeting = $row['targeting'];
    $activity->weighting = $row['weighting'];


    $goals[$row['goal_id']]['activities'][] = $activity;
  }

  $result = array();
  foreach ($goals as $goal_id => $data) {
    $goal = new simple_goal();
    $goal->goal_id = $goal_id;
    $goal->goal_name = $data['goal_name'];

    $goal_with_activities = new goal_with_activities();
    $goal_with_activities->goal = $goal;
    $goal_with_activities->activities = $data['activities'];

    $result[] = $goal_with_activities;
  }

  return $result;
}

generate_json_response(get_goals_with_activities());
