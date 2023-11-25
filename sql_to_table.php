<?php
function sql_to_table($results)
{

  if (is_bool($results)) {
    return '';
  }

  if ($results->num_rows == 0) {
    return '<table><tr><td>Empty Result Set</td></tr></table>';
  }

  $data = array();
  while ($row = mysqli_fetch_assoc($results)) {
    $data[] = $row;
  }

  $table = '<table border="1">';
  $keys = array_keys(reset($data));
  $table .= '<thead><tr>';

  foreach ($keys as $key) {
    $table .= '<th>' . $key . '</th>';
  }
  $table .= '</tr></thead>';

  $table .= '<tbody>';
  foreach ($results as $result) {
    $table .= '<tr>';
    foreach ($result as $val) {
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
