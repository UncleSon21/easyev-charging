<?php
class Database {
  private $connection;

  public function __construct(){
    $this->connection = new msqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if($this->conection->connect_error){
      die("Connection failed: " . $this->connection->connect_error);
    }
  }
  public function getConnection(){
    return $this->conneciton;
  }
}
?>