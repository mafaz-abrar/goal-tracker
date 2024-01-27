<?php
include('./api_utils.php');
include('../framework/db_access.php');

class WeeklyEntry
{
  public int $activity_id;
  public string $activity_name;
  public int $goal_id;
  public string $goal_name;
  public bool $targeting;
  public int $weighting;

  public string $monday_hours;
  public string $tuesday_hours;
  public string $wednesday_hours;
  public string $thursday_hours;
  public string $friday_hours;
  public string $saturday_hours;
  public string $sunday_hours;
}

function get_weekly_entries_list(string $filter_date): array
{
  $db_access = new db_access();
  $activities_sql =
    " SELECT
        entries.activity_id,
        activities.activity_name,
        activities.goal_id,
        goals.goal_name,
        activities.targeting,
        activities.weighting
  
      FROM
        entries
        INNER JOIN activities ON activities.activity_id = entries.activity_id
        INNER JOIN goals ON goals.goal_id = activities.goal_id
      WHERE
        yearweek(entries.date, 1) = yearweek(" . add_single_quotes($filter_date) . ", 1)
      GROUP BY
        entries.activity_id,
        activities.activity_name,
        activities.targeting,
        activities.goal_id,
        goals.goal_name
      UNION
        SELECT
          activities.activity_id,
          activities.activity_name,
          activities.goal_id,
          goals.goal_name,
          activities.targeting,
          activities.weighting
        FROM
          activities
          INNER JOIN goals ON goals.goal_id = activities.goal_id
        WHERE
          activities.targeting = 1
    ";
  $db_access->execute_query($activities_sql);

  $weekly_entries = array();
  while ($row = $db_access->get_next_row()) {
    $weekly_entry = new WeeklyEntry();
    $weekly_entry->activity_id = $row['activity_id'];
    $weekly_entry->activity_name = $row['activity_name'];
    $weekly_entry->goal_id = $row['goal_id'];
    $weekly_entry->goal_name = $row['goal_name'];
    $weekly_entry->targeting = $row['targeting'];
    $weekly_entry->weighting = $row['weighting'];
    $weekly_entries[$weekly_entry->activity_id] = $weekly_entry;
  }

  return $weekly_entries;
}

function populate_weekly_entries_hours(array $weekly_entries, string $filter_date)
{
  $db_access = new db_access();
  $activities_data_sql =
    " SELECT
      sec_to_time(sum(time_to_sec(entries.hours_spent))) AS total_hours_for_date,
      entries.date,
      entries.activity_id
    FROM
      entries
    WHERE
      yearweek(entries.date, 1) = yearweek(" . add_single_quotes($filter_date) . ", 1) 
    GROUP BY
      entries.date,
      entries.activity_id
  ";
  $db_access->execute_query($activities_data_sql);

  while ($row = $db_access->get_next_row()) {
    $weekly_entry = $weekly_entries[$row['activity_id']];

    switch (date('l', strtotime($row['date']))) {
      case 'Monday':
        $weekly_entry->monday_hours = $row['total_hours_for_date'];
        break;
      case 'Tuesday':
        $weekly_entry->tuesday_hours = $row['total_hours_for_date'];
        break;
      case 'Wednesday':
        $weekly_entry->wednesday_hours = $row['total_hours_for_date'];
        break;
      case 'Thursday':
        $weekly_entry->thursday_hours = $row['total_hours_for_date'];
        break;
      case 'Friday':
        $weekly_entry->friday_hours = $row['total_hours_for_date'];
        break;
      case 'Saturday':
        $weekly_entry->saturday_hours = $row['total_hours_for_date'];
        break;
      case 'Sunday':
        $weekly_entry->sunday_hours = $row['total_hours_for_date'];
        break;
    }
  }
}

function weekly_entries_api(): array
{
  $filter_date = date('Y-m-d');
  // $filter_date = date('2023-12-01');

  if (isset($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
  }

  $weekly_entries = get_weekly_entries_list($filter_date);
  populate_weekly_entries_hours($weekly_entries, $filter_date);
  $weekly_entries = array_values($weekly_entries);
  return $weekly_entries;
}

generate_json_response(weekly_entries_api());
