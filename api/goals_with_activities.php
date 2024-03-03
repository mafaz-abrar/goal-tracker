<?php
include_once('../framework/db_access.php');
include_once('../api/api_utils.php');
include_once('./data_structures.php');

function get_goals_with_activities()
{
  $db_access = new db_access();

  $sql =
    " SELECT
      goals.goal_id AS real_goal_id,
      goals.goal_name,
      activities.*
    FROM
      goals
      LEFT JOIN activities ON activities.goal_id = goals.goal_id
  ";

  $db_access->execute_query($sql);

  $goals = array();

  while ($row = $db_access->get_next_row()) {
    $goals[$row['real_goal_id']]['goal_name'] = $row['goal_name'];

    if (!is_null($row['activity_id'])) {
      $activity = new simple_activity();
      $activity->activity_id = $row['activity_id'];
      $activity->goal_id = $row['goal_id'];
      $activity->activity_name = $row['activity_name'];
      $activity->targeting = $row['targeting'];
      $activity->weighting = $row['weighting'];
      $activity->target = $row['target'] / 60;

      $goals[$row['goal_id']]['activities'][] = $activity;
    }
  }

  $result = array();
  foreach ($goals as $goal_id => $data) {
    $goal = new simple_goal();
    $goal->goal_id = $goal_id;
    $goal->goal_name = $data['goal_name'];

    $goal_with_activities = new goal_with_activities();
    $goal_with_activities->goal = $goal;

    if (isset($data['activities'])) {
      $goal_with_activities->activities = $data['activities'];
    } else {
      $goal_with_activities->activities = [];
    }

    $result[] = $goal_with_activities;
  }

  return $result;
}

generate_json_response(get_goals_with_activities());
