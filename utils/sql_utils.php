<?php

include_once(__DIR__ . '/../framework/db_access.php');

function sql_to_table(db_access $db_access, array $column_aliases = [])
{

  if (is_bool($db_access)) {
    return '';
  }

  if ($db_access->get_num_rows() == 0) {
    return "<p class='empty-result'>Empty Result Set</p>";
  }

  $rows = array();
  while ($row = $db_access->get_next_row()) {
    $rows[] = $row;
  }

  $table = '<table>';
  $table .= '<thead><tr>';

  $keys = array_keys(reset($rows));
  if ($column_aliases != []) {
    $keys = $column_aliases;
  }

  foreach ($keys as $key) {
    $table .= '<th>' . $key . '</th>';
  }
  $table .= '</tr></thead>';

  $table .= '<tbody>';

  foreach ($rows as $row) {
    $table .= '<tr>';
    foreach ($row as $val) {
      $table .= '<td>' . $val . '</td>';
    }
    $table .= '</tr>';
  }
  $table .= '</tbody></table>';
  return $table;
}

function add_single_quotes($string)
{
  return "'" . $string . "'";
}
