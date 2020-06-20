<?php

require_once ( $APPS_PHP_DIR . '/RedDogBusCompany.namespace.php' ) ;

$view = new RdbcPhpDrivenView() ;
$request = $view->getRequest() ;

$dataStoreManager = new RdbcMysqlDataStoreManager() ;
$dataStoreManager->open() ;
$controller = new RdbcController( $dataStoreManager ) ;

$controller->setRequest( $request ) ;
$controller->processRequest() ;

$result = $controller->getResult() ;
$view->processResult( $result ) ;

$dataStoreManager->close() ;

echo '<?xml version="1.0" encoding="UTF-8"?>' , "\n" ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<!--
 Red Dog Bus Company Online ticket Purchase System
 Version 0.0.0, date/last/modified
 Ed Arvelaez

 Red Dog Bus Company is an e-sale website simulator that features seemless
 server-side request-response interaction. It features php html session and
 post back control, unobstrusive javascript, and css reset, and w3c 1.1 xhtml
 validation.
-->

<meta name="description" content="Red Dog Bus Company Online ticket Purchase System" />
<meta name="keywords" content="htmlsession,postbackcontrol,webpagecontroller" />
<meta name="author" content="Ed Arvelaez" />
<meta http-equiv="content-type"  content="text/html;charset=utf-8" />
<!-- <meta charset="utf-8" /> -->
<link type="text/css" rel="stylesheet" media="screen" href="/common/css/reset.css" />
<link type="text/css" rel="stylesheet" media="screen" href="/apps/reddogbuscompany/style.css" />
<script type="text/javascript" src="reddogbuscompany.js"></script>
<title>Red Dog Bus Company - Online Ticket Purchase System</title>
</head>
<body id="bodyRedDogBusCompany" class="cssBodyRedDogBusCompany">
<form id="formRedDogBusCompany" method="post" action="<?php echo $_SERVER[ 'REQUEST_URI' ] ; ?>" enctype="application/x-www-form-urlencoded">
<div id="divTopBanner" class="cssDivTopBanner">
 Red Dog Bus Company - Online Ticket Purchase System
 <a href="http://validator.w3.org/check?uri=referer">
  <img src="http://www.w3.org/Icons/valid-xhtml11" alt="Valid XHTML 1.1" height="31" width="88" />
 </a>
</div>
<div id="divRouteInfo" class="cssDivRouteInfo">
 <table id="tblRouteInfo" class="cssTblRouteInfo">
  <tr id="trRouteInfoHeadings" class="cssTrRouteInfoHeadings">
   <th>Route</th>
   <th>Ticket Price</th>
   <th>Available Seats</th>
  </tr>
  <?php $view->printRouteInfoRows() ; ?>
 </table>
</div>
<div id="divTicketPurchaseConsole" class="cssDivTicketPurchaseConsole">
 <div id="divRouteSelection" class="cssDivRouteSelection">
  <div id="divStepOne" class="cssStep">Step 1 : Select a route</div>
  <select id="sltRouteSelection" name="sltRouteSelection">
   <?php $view->printRouteOptions() ; ?>
  </select>
 </div>
 <div id="divTicketNumberSelection" class="cssDivTicketNumberSelection">
  <table>
   <tr>
    <th<?php $view->printColSpanAttribute() ; ?> id="thStepTwo" class="cssStep">Step 2 : Select the number of tickets to purchase</th>
   </tr>
   <?php $view->printTicketRadioButtons() ; ?>
  </table>
 </div>
 <div id="divTicketPurchase" class="cssDivTicketPurchase">
  <div id="divStepThree" class="cssStep">Step 3 : Complete the purchase</div>
  <input id="btnPurchase" name="btnPurchase" type="submit" value="Purchase"<?php $view->printButtonDisabledAttribute() ; ?> />
 </div>
</div>
<div id="divTicketPurchaseStatus" class="<?php $view->printPuchaseStatusStyle() ; ?>">
 <?php $view->printPurchaseStatusText() ; ?>
</div>
<div id="divGlobalPhpStateInfo" class="cssDivGlobalPhpStateInfo">
 <?php $view->printPostbackControlElements() ; ?>
</div>
<div class="cssDivSeeTheCode">
  See the code:
  <select id="sltSeeTheCode">
    <option>Select a script</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=Reddogbuscompany_sql.txt">Database implementation</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcBusRoute_class_php.txt">Bus route class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcMysqlDataStoreManager_class_php.txt">Data Store Manager class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcMysqlDataStore_class_php.txt">Data Store class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcRequest_class_php.txt">Request class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcResult_class_php.txt">Result class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcController_class_php.txt">Controller class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=RdbcPhpDrivenView_class_php.txt">View class</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=reddogbuscompany_js.txt">Client script</option>
    <option value="http://<?php echo $HOST_NAME ; ?>/apps/codedisplay/codedisplay.php?project=reddogbuscompany&amp;file=index_php.txt">Preprocessed markup</option>
  </select>
</div>
</form>
</body>
</html>