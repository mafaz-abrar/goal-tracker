<?php
include(dirname(__FILE__) . '/db_config.php');

class db_access
{
  private $conn;

  public function __construct($db_name = "test")
  {
    $this->conn = mysqli_connect(HOST, USER, PSWD, $db_name);
  }

  public function execute_query($sql_statement)
  {
    return mysqli_query($this->conn, $sql_statement);
  }

  public function save_new_object(data_object $object)
  {
    $object->update();

    $statement = "INSERT INTO ";
    $statement .= $object->table_name . " (";

    $fields = array_keys($object->data);

    foreach ($fields as $field) {
      $last_key = array_key_last($fields);

      if ($field == $fields[$last_key]) {
        $statement .= $field;
        break;
      }

      $statement .= $field . ", ";
    }

    $statement .= ") VALUES ( ";

    foreach ($object->data as $key => $val) {
      echo $val;

      if ($key == array_key_last($object->data)) {
        $statement .= $val;
        break;
      }

      $statement .= $val . ", ";
    }

    $statement .= ")";

    $this->execute_query($statement);
  }
}

class data_object
{
  public string $id;
  public string $table_name;
  public array $data;

  public function __construct(string $table_name)
  {
    $this->update();
    $this->table_name = $table_name;
  }

  public function update()
  {
    $this->data = array();
  }
}

class entry extends data_object
{
  public $goal_id;
  public $activity_id;
  public $date;
  public $task_description;
  public $hours;
  public $start_time;
  public $end_time;

  public function __construct()
  {
    $this->goal_id = 0;
    $this->activity_id = 0;
    $this->date = '';
    $this->task_description = '';
    $this->hours = '';
    $this->start_time = '';
    $this->end_time = '';

    parent::__construct('entries');
  }

  public function update()
  {
    $this->data = [
      'goal_id' => $this->goal_id,
      'activity_id' => $this->activity_id,
      'date' => $this->date,
      'task_description' => $this->task_description,
      'hours_spent' => $this->hours,
      'start_time' => $this->start_time,
      'end_time' => $this->end_time
    ];
  }
}

class goal extends data_object
{
  public $goal_name;

  public function __construct()
  {
    $this->goal_name = '';

    parent::__construct('goals');
  }

  public function update()
  {
    $this->data = [
      'goal_name' => $this->goal_name
    ];
  }
}

class activity extends data_object
{
  public $goal_id;
  public $activity_name;

  public function __construct()
  {
    $this->goal_id = '';
    $this->activity_name = '';

    parent::__construct('activities');
  }

  public function update()
  {
    $this->data = [
      'goal_id' => $this->goal_id,
      'activity_name' => $this->activity_name
    ];
  }
}
