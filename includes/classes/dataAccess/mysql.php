<?php
  class dataAccess_mysql implements dataAccessConnector {
      private $dbLink;
      private $server = DB_SERVER;
      private $username = DB_SERVER_USERNAME;
      private $password = DB_SERVER_PASSWORD;
      private $databaseName = DB_DATABASE;
      
      public function __construct($server = false, $user = false, $pass = false, $dbName = false){
          if ($server !== false){
              $this->server = $server;
          }
          
          if ($user !== false){
              $this->username = $user;
          }
          
          if ($pass !== false){
              $this->password = $pass;
          }
          
          if ($dbName !== false){
              $this->databaseName = $dbName;
          }
      }
      
      public function connect(){
          $this->dbLink = mysql_connect($this->server, $this->username, $this->password, false);
          mysql_select_db($this->databaseName);
      }
      
      public function disconnect(){
        return mysql_close($this->dbLink);
      }
      
      public function query($queryString){
          $return = array(
              'queryResource' => @mysql_query($queryString)
          );
          
          $possibleError = @mysql_errno($this->dbLink);
          if ($possibleError > 0){
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . mysql_error($this->dbLink);
          }
        return $return;
      }
      
      public function fetchArray($queryResource){
          $return = array(
              'fetchResource' => @mysql_fetch_array($queryResource, MYSQL_ASSOC)
          );
          
          $possibleError = @mysql_errno($this->dbLink);
          if ($possibleError > 0){
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . mysql_error($this->dbLink);
          }
        return $return;
      }
      
      public function fetchObject($queryResource){
        return mysql_fetch_object($queryResource);
      }
      
      public function numberOfRows($queryResource){
          $return = array(
              'numberOfRows' => @mysql_num_rows($queryResource)
          );
          
          $possibleError = @mysql_errno($this->dbLink);
          if ($possibleError > 0){
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . mysql_error($this->dbLink);
          }
        return $return;
      }
      
      public function insertId(){
        return mysql_insert_id($this->dbLink);
      }
      
      public function getError($errorNumber){
          /*
           * @todo: Make custom error messages for option later ( show basic/complete error messages )
           */
          switch($errorNumber){
              default:
                  $errMsg = 'An error has occured with the following query:';
              break;
          }
        return $errMsg;
      }
      
      public function freeResult($queryResource){
        return mysql_free_result($queryResource);
      }
      
      public function cleanInput($string){
        return mysql_real_escape_string($string, $this->dbLink);
      }
      
      public function cleanOutput($string){
        return $string;
      }
      
      public function dataSeek($queryResource, $rowNumber){
          $return = array(
              'result' => @mysql_data_seek($queryResource, $rowNumber)
          );
          
          $possibleError = @mysql_errno($this->dbLink);
          if ($possibleError > 0){
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . mysql_error($this->dbLink);
          }
        return $return;
      }
  }
?>