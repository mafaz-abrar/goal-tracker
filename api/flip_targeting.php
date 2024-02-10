<?php
include_once('../framework/db_access.php');
include_once('../api/api_utils.php');

function flip_targeting(int $activity_id)
{
  $db_access = new db_access();
  $activity = new activity($db_access, $activity_id);
  $activity->targeting = !$activity->targeting;
  $activity->update_existing();
}

// $res = new Response($_POST['activity_id']);
// $res->generate();

// generate_json_response('Hey');

try {
  flip_targeting($_POST['activity_id']);
} catch (Exception $ex) {
  generate_json_response($ex->getMessage());
  exit();
}


generate_json_response('Success');
