<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

class RdbcDebugDataStoreManager implements RdbcIDataStoreManager
{

private $_dataStore = NULL ;

private function _initialize ( )
{
  $this->_dataStore = new RdbcDebugDataStore() ;
}

public function __construct ( )
{
  $this->_initialize() ;
}

public function RdbcDebugDataStoreManager ( )
{
  $this->_initialize() ;
}

public function isOpen ( )
{
  return true ;
}

public function open ( )
{
  ;
}

public function getDataStore ( $pToken )
{
  return $this->_dataStore ;
}

public function createDataStore ( )
{
  return $this->_dataStore ;
}
public function deleteDataStoreByToken ( $pToken )
{
  ;
}

public function close ( )
{
  ;
}

}

?>