<?php
include_once('../framework/db_access.php');

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
  public string $activity_name;
  public simple_entry $entry;
}

class day_with_expanded_entries
{
  public string $date;
  public array $expanded_entries;
}
