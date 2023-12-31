<?php

function generate_json_response(mixed $data_to_append)
{
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");
  echo json_encode($data_to_append);
}
