<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ERROR | E_PARSE);

define('DATABASE', 'hap56');
define('USERNAME', 'hap56');
define('PASSWORD', 'dApqjS3S');
define('CONNECTION', 'sql2.njit.edu');
class dbConn{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
    private function __construct() {
     try {
     // PDO object to a db variable.
            self::$db = new PDO('mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD);
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
    catch (PDOException $e) {
        //Output error - would normally log this to error file.
         echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        if (!self::$db) {
     
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
class collection {
protected $html;
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {

protected $tableName;
public function save()
    
    {
        if ($this->id != '') {
            $sql = $this->update($this->id);
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    private function insert() {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    private function update($id) {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if( ! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
    public function delete($id) {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    

class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
        $tableName='accounts';
        return $tableName;
    }
}

class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
}

echo "<h1><center>Accounts Table</center></h1>";
echo "<h2>Search accounts table</h2>";
$records = accounts::findAll();
 // to print all accounts records in html table  
  $html = '<table border = 5><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows
    
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
    print_r($html);

    
    echo "<h2>Search account table by id</h2>";
   $record = accounts::findOne(11);
 // Displaying Header Row 
  
  $html = '<table border = 3><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
    
    print_r($html);
//-------------------------- Insert Record---------------------
 echo "<h2>Insert One Record</h2>";
$record = new account();
$record->email="harsh@gmail.com";
$record->fname="harsh";
$record->lname="patel";
$record->phone="201-666-8788";
$record->birthday="1994-01-10";
$record->gender="male";
$record->password="jimmy";
$record->save();
$records = accounts::findAll();
$html = '<table border = 4><tbody>';
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
     {
    $html .= '<th>' . htmlspecialchars($key) . '</th>';
      }
       
    $html .= '</tr>';
    // Displayng Data Rows
    
    
    foreach($records as $key=>$value)
    {
      $html .= '<tr>';    
      foreach($value as $key2=>$value2)
      {
      $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
      }
        $html .= '</tr>';
    }
 
    $html .= '</tbody></table>';
echo "<h2>After Inserting</h2>";
print_r($html);
echo "<h2>Delete one Record</h2>";
$record= new account();
$id=6;
$record->delete($id);
echo '<h2>Record with id: '.$id.' is deleted</h2>';

$record = accounts::findAll();

$html = '<table border = 4><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows
    
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
echo "<h3>After Deleting</h3>";
print_r($html);

echo "<h1>Update One Record</h1>";
$id=4;
$record = new account();
$record->id=$id;
$record->fname="Jack";
$record->lname="Shaw";
$record->gender="male";
$record->save();
$record = accounts::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 4><tbody>';
  // Display Header Row
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
 
 print_r($html);


 echo"<h1><center>Todos Table</center></h1>";

 echo "<h2>Search all for todo table</h2>";
 $records = todos::findAll();

  $html = '<table border = 5><tbody>';

  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
    {
    $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
       
    $html .= '</tr>';
    
    
    foreach($records as $key=>$value)
    {
    $html .= '<tr>';
        
    foreach($value as $key2=>$value2)
        {
         $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
    echo "Todo table";
    print_r($html);
    echo"<h2>Search by uniqui id</h2>";
 $record = todos::findOne(3);

  print_r("Todo table id - 3");
  
  $html = '<table border = 5><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
     {
     $html .= '<th>' . htmlspecialchars($key) . '</th>';
     }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';   
    }
    $html .= '</tbody></table>';
    
    print_r($html);
   echo "<h2>Insert One Record</h2>";
        $record = new todo();
        $record->owneremail="hp@njit.edu";
        $record->ownerid='90';
        $record->createddate="11-09-2017";
        $record->duedate="11-13-2017";
        $record->message="New Data Inserted";
        $record->isdone=1;
        $record->save();
        $records = todos::findAll();
        echo"<h2>After Inserting</h2>";
 
     $html = '<table border = 5><tbody>';
  
      $html .= '<tr>';
      foreach($records[0] as $key=>$value)
         {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';    
    
    foreach($records as $key=>$value)
    {
    $html .= '<tr>';    
    foreach($value as $key2=>$value2)
     {
       $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
     }
       $html .= '</tr>';  
    }
    $html .= '</tbody></table>';

print_r($html);
echo "<h1>Delete One Record</h1>";
$record= new todo();
$id=7;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';

$record = todos::findAll();

$html = '<table border = 5><tbody>';
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
echo "<h3>After Deleting</h3>";
print_r($html);
echo "<h1>Update One Record</h1>";
$id=4;
$record = new todo();
$record->id=$id;
$record->owneremail="hap56@gmail.com";
$record->ownerid="90";
$record->createddate="2017-01-02 00:00:00";
$record->duedate="2017-09-06 00:00:00";
$record->message="HELLO";
$record->isdone="1";
$record->save();
$record = todos::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 5><tbody>';
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
    {
     $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
       
    $html .= '</tr>';
   
    foreach($record as $key=>$value)
    {
     $html .= '<tr>';
        
     foreach($value as $key2=>$value2)
     {
     $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
     }
      $html .= '</tr>';
          
    }
    $html .= '</tbody></table>';
 
 print_r($html);
?>