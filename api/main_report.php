<?php
include('./api_utils.php');
include('../framework/db_access.php');
include_once('./data_structures.php');

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
        activities.weighting,
        activities.target
  
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
          activities.weighting,
          activities.target
        FROM
          activities
          INNER JOIN goals ON goals.goal_id = activities.goal_id
        WHERE
          activities.targeting = 1
    ";
  $db_access->execute_query($activities_sql);

  $weekly_entries = array();
  while ($row = $db_access->get_next_row()) {
    $activity = new simple_activity();
    $activity->activity_id = $row['activity_id'];
    $activity->activity_name = $row['activity_name'];
    $activity->goal_id = $row['goal_id'];
    $activity->targeting = $row['targeting'];
    $activity->weighting = $row['weighting'];
    $activity->target = $row['target'] / 60;

    $weekly_entry = new weekly_entry();
    $weekly_entry->goal_name = $row['goal_name'];
    $weekly_entry->activity = $activity;
    $weekly_entry->monday_time = 0;
    $weekly_entry->tuesday_time = 0;
    $weekly_entry->wednesday_time = 0;
    $weekly_entry->thursday_time = 0;
    $weekly_entry->friday_time = 0;
    $weekly_entry->saturday_time = 0;
    $weekly_entry->sunday_time = 0;

    $weekly_entries[$row['activity_id']] = $weekly_entry;
  }

  return $weekly_entries;
}

function populate_weekly_entries_hours(array $weekly_entries, string $filter_date)
{
  $db_access = new db_access();
  $activities_data_sql =
    " SELECT
      sum(time_spent) AS total_sec_for_date,
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
        $weekly_entry->monday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Tuesday':
        $weekly_entry->tuesday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Wednesday':
        $weekly_entry->wednesday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Thursday':
        $weekly_entry->thursday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Friday':
        $weekly_entry->friday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Saturday':
        $weekly_entry->saturday_time = $row['total_sec_for_date'] / 60;
        break;
      case 'Sunday':
        $weekly_entry->sunday_time = $row['total_sec_for_date'] / 60;
        break;
    }
  }
}

function weekly_entries_api(): array
{
  $filter_date = date('Y-m-d');
  // $filter_date = date('2023-12-01');

  if (isset($_GET['filter_date'])) {
    $filter_date = $_GET['filter_date'];
  }

  $weekly_entries = get_weekly_entries_list($filter_date);
  populate_weekly_entries_hours($weekly_entries, $filter_date);
  $weekly_entries = array_values($weekly_entries);
  return $weekly_entries;
}

generate_json_response(weekly_entries_api());
