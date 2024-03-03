<?php
include_once('../framework/db_access.php');


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
  public int $target;
}

class simple_entry
{
  public int $entry_id;
  public int $activity_id;
  public string $date;
  public string $task_description;
  public string $time_spent;
  public ?string $start_time;
  public ?string $end_time;
}

class expanded_entry
{
  public string $goal_name;
  public int $goal_id;
  public string $activity_name;
  public simple_entry $entry;
}

class day_with_expanded_entries
{
  public string $date;
  public array $expanded_entries;
}

class weekly_entry
{
  public simple_activity $activity;
  public string $goal_name;

  public int $monday_time;
  public int $tuesday_time;
  public int $wednesday_time;
  public int $thursday_time;
  public int $friday_time;
  public int $saturday_time;
  public int $sunday_time;
}

class goal_with_activities
{
  public simple_goal $goal;
  public array $activities;
}
