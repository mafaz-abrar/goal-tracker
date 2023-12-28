<?php

function append_to_response(mixed $data_to_append) {
  echo json_encode($data_to_append);
}