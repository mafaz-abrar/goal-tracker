<?php
include_once('../framework/db_access.php');
include_once('./api_utils.php');

function get_current_score(string $date): string
{
  $sql =
    " SELECT 
        sum(activities.weighting) AS score
      FROM
        entries
        INNER JOIN activities ON entries.activity_id = activities.activity_id
      WHERE
        yearweek(entries.date, 1) = yearweek(" . add_single_quotes($date) . ", 1)
  ";

  $db_access = new db_access();
  $db_access->execute_query($sql);

  $data = array();
  while ($row = $db_access->get_next_row()) {
    $data['score'] = $row['score'];
  }

  if (is_null($data['score'])) $data['score'] = 0;

  return $data['score'];
}

generate_json_response(get_current_score($_GET['filter_date']));
