<?php
include_once('../api/api_utils.php');
include_once(__DIR__ . '/db_config.php');
include_once(__DIR__ . '/../utils/sql_utils.php');

class db_access
{
  private mysqli | false $conn;
  private mysqli_result | bool | null $result;

  public function __construct()
  {
    $this->conn = mysqli_connect(HOST, USER, PSWD, DB);
    $this->result = null;
  }

  public function __destruct()
  {
    if (!is_bool($this->conn))
      mysqli_close($this->conn);
  }

  public function execute_query($sql_statement)
  {
    $this->result = mysqli_query($this->conn, $sql_statement);
  }

  public function query_success(): bool
  {
    if (is_bool($this->result)) return $this->result;
    return true;
  }

  public function get_next_row(): array | null
  {
    return mysqli_fetch_assoc($this->result);
  }

  public function has_rows(): bool
  {
    return ($this->get_num_rows() > 0);
  }

  public function get_num_rows(): string | int
  {
    return mysqli_num_rows($this->result);
  }

  public function get_error(): string
  {
    return mysqli_error($this->conn);
  }

  public function insert_new_object(data_object $object)
  {
    $statement = "INSERT INTO ";
    $statement .= $object->get_table_name() . " (";

    $fields = array_keys($object->get_data());

    foreach ($fields as $index => $field) {
      if ($index === array_key_last($fields)) {
        $statement .= $field;
        break;
      }

      $statement .= $field . ", ";
    }

    $statement .= " ) VALUES ( ";

    foreach ($object->get_data() as $key => $val) {
      if ($key === array_key_last($object->get_data())) {
        $statement .= $val;
        break;
      }

      $statement .= $val . ", ";
    }

    $statement .= " ) ";

    $this->execute_query($statement);
  }

  public function update_existing_object(data_object $object)
  {
    $statement = 'UPDATE ';
    $statement .= $object->get_table_name() . ' ';

    $statement .= 'SET ';

    foreach ($object->get_data() as $key => $val) {
      if ($key === array_key_last($object->get_data())) {
        $statement .= $key . ' = ' . $val . ' ';
        break;
      }

      $statement .= $key . ' = ' . $val . ", ";
    }

    $statement .= 'WHERE ' . $object->get_id_column_name() . ' = ' . $object->get_id();

    $this->execute_query($statement);
  }

  public function get_existing_object_data(data_object $object): array
  {
    $statement = 'SELECT * FROM ' . $object->get_table_name() . ' ';
    $statement .= 'WHERE ' . $object->get_id_column_name() . ' = ' . $object->get_id();

    $this->execute_query($statement);
    $num_rows = $this->get_num_rows();

    if ($num_rows !== 1) {
      throw new Exception('Expect exactly 1 record!');
    }

    return $this->get_next_row();
  }

  public function delete_existing_object(data_object $object)
  {
    $statement = 'DELETE FROM ' . $object->get_table_name() . ' ';
    $statement .= 'WHERE ' . $object->get_id_column_name() . ' = ' . $object->get_id();

    $this->execute_query($statement);
  }
}

abstract class data_object
{
  protected ?int $id;
  protected string $id_column_name;
  protected string $table_name;
  protected array $data;
  protected db_access $db_access;

  protected function __construct(
    db_access $db_access,
    string $id_column_name,
    string $table_name,
    ?int $id,
  ) {
    $this->db_access = $db_access;
    $this->id_column_name = $id_column_name;
    $this->table_name = $table_name;

    $this->id = $id;

    if (!is_null($id)) {
      $this->load_existing();
      $this->save_data();
    }
  }

  abstract protected function save_data();
  abstract protected function load_data(array $data);

  public function get_id(): int
  {
    return $this->id;
  }

  public function get_id_column_name(): string
  {
    return $this->id_column_name;
  }

  public function get_table_name(): string
  {
    return $this->table_name;
  }

  public function get_data(): array
  {
    return $this->data;
  }

  public function insert_new()
  {
    $this->save_data();
    $this->db_access->insert_new_object($this);
  }

  public function update_existing()
  {
    if ($this->id === '') {
      throw new Exception('No ID provided!');
    }

    $this->save_data();
    $this->db_access->update_existing_object($this);
  }

  private function load_existing()
  {
    if ($this->id === '') {
      throw new Exception('No ID provided!');
    }

    $raw_data_from_db = $this->db_access->get_existing_object_data($this);
    $this->id = $raw_data_from_db[$this->id_column_name];
    $this->load_data($raw_data_from_db);
  }

  public function delete_existing()
  {
    if ($this->id === '') {
      throw new Exception('No ID provided!');
    }

    $this->db_access->delete_existing_object($this);
  }
}

class entry extends data_object
{
  public ?int $activity_id;
  public ?string $date;
  public ?string $task_description;
  public ?string $time_spent;
  public ?string $start_time;
  public ?string $end_time;

  public function __construct(db_access $db_access, int $id = null)
  {
    $this->activity_id = null;
    $this->date = null;
    $this->task_description = null;
    $this->time_spent = null;
    $this->start_time = null;
    $this->end_time = null;

    parent::__construct($db_access, 'entry_id', 'entries', $id);
  }

  protected function save_data()
  {
    if (is_null($this->activity_id)) {
      throw new Exception("Activity ID is null!");
    }

    if (is_null($this->date)) {
      throw new Exception("Date is null!");
    }

    if (is_null($this->task_description)) {
      throw new Exception("Task description is null!");
    }

    if (is_null($this->time_spent)) {
      throw new Exception("Time spent is null!");
    }

    $this->data = [
      'activity_id' => $this->activity_id,
      'date' => add_single_quotes($this->date),
      'task_description' => add_single_quotes($this->task_description),
      'time_spent' =>  add_single_quotes($this->time_spent),
      'start_time' => is_null($this->start_time) ? "null" : add_single_quotes($this->start_time),
      'end_time' => is_null($this->end_time) ? "null" : add_single_quotes($this->end_time)
    ];
  }

  protected function load_data(array $data)
  {
    $this->activity_id = $data['activity_id'];
    $this->date = $data['date'];
    $this->task_description = $data['task_description'];
    $this->time_spent  = $data['time_spent'];
    $this->start_time = $data['start_time'];
    $this->end_time = $data['end_time'];
  }
}

class goal extends data_object
{
  public ?string $goal_name;

  public function __construct(db_access $db_access, int $id = null)
  {
    $this->goal_name = null;

    parent::__construct($db_access, 'goal_id', 'goals', $id);
  }

  protected function save_data()
  {
    if (is_null($this->goal_name)) {
      throw new Exception("Goal name is null!");
    }

    $this->data = [
      'goal_name' => add_single_quotes($this->goal_name)
    ];
  }

  protected function load_data(array $data)
  {
    $this->goal_name = $data['goal_name'];
  }
}

class activity extends data_object
{
  public ?int $goal_id;
  public ?string $activity_name;
  public ?bool $targeting;
  public ?int $weighting;
  public ?int $target;

  public function __construct(db_access $db_access, int $id = null)
  {
    $this->goal_id = null;
    $this->activity_name = null;
    $this->targeting = null;
    $this->weighting = null;
    $this->target = null;

    parent::__construct($db_access, 'activity_id', 'activities', $id);
  }

  protected function save_data()
  {
    if (is_null($this->goal_id)) {
      throw new Exception("Goal ID is null!");
    }

    if (is_null($this->activity_name)) {
      throw new Exception("Activity name is null!");
    }

    if (is_null($this->targeting)) {
      throw new Exception('Targeting is null!');
    }

    if (is_null($this->weighting)) {
      throw new Exception('Weighting is null!');
    }

    if (is_null($this->target)) {
      throw new Exception('Target is null!');
    }

    $this->data = [
      'goal_id' => $this->goal_id,
      'activity_name' => add_single_quotes($this->activity_name),
      'targeting' => $this->targeting ? (string) 1 : (string) 0,
      'weighting' => $this->weighting,
      'target' => $this->target
    ];
  }

  protected function load_data(array $data)
  {
    $this->goal_id = $data['goal_id'];
    $this->activity_name = $data['activity_name'];
    $this->targeting = (int) $data['targeting'] === 1;
    $this->weighting = $data['weighting'];
    $this->target = $data['target'];
  }

  public function get_goal_name(): string
  {
    if (is_null($this->id)) {
      throw new Exception('Activity ID is null!');
    }

    $sql =
      " SELECT 
        goal_name 
      FROM 
        goals
        INNER JOIN activities ON activities.goal_id = goals.goal_id
          AND activities.activity_id = " . $this->id . ";";

    $this->db_access->execute_query($sql);
    if ($this->db_access->has_rows()) {
      return $this->db_access->get_next_row()['goal_name'];
    } else {
      throw new Exception('No goals found!');
    }
  }
}
