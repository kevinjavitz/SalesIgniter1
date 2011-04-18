<?php
  class dataAccess_pdo_mysql implements dataAccessConnector {
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
          $this->dbLink = new PDO(
              'mysql:host=' . $this->server . ';port=3306;dbname=' . $this->databaseName,
              $this->username,
              $this->password
          );
      }
      
      public function disconnect(){
        return $this->dbLink = null;
      }
      
      public function query($queryString){
          $return = array(
              'queryResource' => @$this->dbLink->query($queryString)
          );
          
          $possibleError = @$this->dbLink->errorCode();
          if ($possibleError > 0){
              $errInfo = @$this->dbLink->errorInfo();
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . $errInfo['2'];
          }
        return $return;
      }
      
      public function fetchArray($queryResource){
          $return = array(
              'fetchResource' => @$queryResource->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)
          );
          
          $possibleError = @$this->dbLink->errorCode();
          if ($possibleError > 0){
              $errInfo = @$this->dbLink->errorInfo();
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . $errInfo['2'];
          }
        return $return;
      }
      
      public function fetchObject($queryResource){
        return mysql_fetch_object($queryResource);
      }
      
      public function numberOfRows($queryResource){
          $return = array(
              'numberOfRows' => @$queryResource->rowCount()
          );
          
          $possibleError = @$this->dbLink->errorCode();
          if ($possibleError > 0){
              $errInfo = @$this->dbLink->errorInfo();
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . $errInfo['2'];
          }
        return $return;
      }
      
      public function insertId(){
        return $this->dbLink->lastInsertId();
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
        return addslashes($string);
      }
      
      public function cleanOutput($string){
        return $string;
      }
      
      public function dataSeek($queryResource, $rowNumber){
          $return = array(
              'result' => @$queryResource->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $rowNumber)
          );
          
          $possibleError = @$this->dbLink->errorCode();
          if ($possibleError > 0){
              $errInfo = @$this->dbLink->errorInfo();
              $return['errMsg'] = $this->getError($possibleError);
              $return['serverErrMsg'] = '<b>MySQL Error Number:</b> ' . $possibleError . '<br>' . 
                                        '<b>MySQL Error:</b> ' . $errInfo['2'];
          }
        return $return;
      }
  }
?>