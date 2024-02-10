<?php
include('./api_utils.php');
include_once('../framework/db_access.php');
include('./data_structures.php');

$date = $_GET['filter_date'];

$all_sql =
  " SELECT
      goals.goal_name,
      activities.activity_name,
      entries.date,
      entries.activity_id, 
      entries.task_description,
      entries.time_spent,
      entries.start_time,
      entries.end_time
    FROM
      entries
      INNER JOIN activities ON activities.activity_id = entries.activity_id
      INNER JOIN goals ON goals.goal_id = activities.goal_id
    WHERE
      yearweek(entries.date, 1) = yearweek(" . add_single_quotes($date) . ", 1)
    ORDER BY
      entries.date ASC
  ";

// generate_json_response($all_sql);
// exit();

$db_access = new db_access();
$db_access->execute_query($all_sql);

$entries = array();
while ($row = $db_access->get_next_row()) {
  $entries[] = $row;
}

$days_array_raw = array();

foreach ($entries as $entry_raw) {
  $expanded_entry = new expanded_entry();
  $expanded_entry->goal_name = $entry_raw['goal_name'];
  $expanded_entry->activity_name = $entry_raw['activity_name'];

  $entry = new simple_entry();
  $entry->entry_id = -1;
  $entry->activity_id = $entry_raw['activity_id'];
  $entry->date = $entry_raw['date'];
  $entry->task_description = $entry_raw['task_description'];
  $entry->time_spent = $entry_raw['time_spent'];
  $entry->start_time = $entry_raw['start_time'];
  $entry->end_time = $entry_raw['end_time'];

  $expanded_entry->entry = $entry;

  // group entries into an arrays by date
  $days_array_raw[$entry_raw['date']][] = $expanded_entry;
}

$days_array = array();

foreach ($days_array_raw as $date => $entries) {
  $day_obj = new day_with_expanded_entries();
  $day_obj->date = $date;
  $day_obj->expanded_entries = $entries;
  $days_array[] = $day_obj;
}

generate_json_response($days_array);
