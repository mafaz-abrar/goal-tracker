<?php

function add_headers()
{
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: *");
}

function generate_json_response(mixed $data_to_append)
{
  add_headers();
  echo json_encode($data_to_append);
}

class Response
{
  private array $data;

  public function __construct(mixed $data_to_append = null)
  {
    $this->data = [];
    if (!is_null($data_to_append)) $this->append($data_to_append);
  }

  public function append(mixed $data_to_append)
  {
    $this->data[] = $data_to_append;
  }

  public function generate()
  {
    add_headers();
    echo json_encode($this->data);
  }
}
