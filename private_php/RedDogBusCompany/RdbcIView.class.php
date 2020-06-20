<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

interface RdbcIView
{

public function getRequest ( ) ;
public function processResult( $pResult ) ;

}

?>