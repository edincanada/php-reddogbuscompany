<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

interface RdbcIDataStoreManager
{

public function isOpen ( ) ;
public function open ( ) ;
public function getDataStore ( $pToken ) ;
public function createDataStore ( ) ;
public function deleteDataStoreByToken ( $pToken ) ;
public function close ( ) ;

}

?>