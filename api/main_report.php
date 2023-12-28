<?php
include('./headers.php');
include('./api_utils.php');
include('../framework/db_access.php');

class WeeklyEntry
{
  public int $activity_id;
  public string $activity_name;
  public int $goal_id;
  public string $goal_name;

  public string $monday_hours;
  public string $tuesday_hours;
  public string $wednesday_hours;
  public string $thursday_hours;
  public string $friday_hours;
  public string $saturday_hours;
  public string $sunday_hours;
}

$db_access = new db_access();
$this_week_activities_data_sql =
  " SELECT
      sec_to_time(sum(time_to_sec(entries.hours_spent))) AS total_hours_for_date,
      entries.date,
      entries.activity_id,
      activities.activity_name,
      activities.goal_id,
      goals.goal_name
    FROM
      entries
      INNER JOIN activities ON activities.activity_id = entries.activity_id
      INNER JOIN goals ON goals.goal_id = activities.goal_id
    WHERE
      yearweek(entries.date, 1) = yearweek(" . add_single_quotes($date) . ", 1) 
    GROUP BY
      entries.date,
      entries.activity_id,
      activities.activity_name,
      activities.goal_id,
      goals.goal_name
    ORDER BY
      goals.goal_name,
      activities.activity_name
  ";
$db_access->execute_query($this_week_activities_data_sql);

$activities_data = array();


append_to_response($activities_data);
