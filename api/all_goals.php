<?php
include_once('./api_utils.php');
include_once('../framework/db_access.php');

class simple_goal
{
  public int $goal_id;
  public string $goal_name;
}

function get_all_goals(): array
{
  $sql =
    " SELECT 
      *
    FROM
      goals
  ";

  $db_access = new db_access();
  $db_access->execute_query($sql);

  $entries = array();
  while ($row = $db_access->get_next_row()) {
    $goal = new simple_goal();
    $goal->goal_id = $row['goal_id'];
    $goal->goal_name = $row['goal_name'];

    $entries[] = $goal;
  }

  return $entries;
}

generate_json_response(get_all_goals());
