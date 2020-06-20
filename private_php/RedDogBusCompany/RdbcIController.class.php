<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

interface RdbcIController
{

public function setRequest ( $pRequest ) ;
public function processRequest ( ) ;
public function getResult ( ) ;

}

?>