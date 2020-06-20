<?php

/* echo '<?xml version="1.0" encoding="UTF-8"?>' , "\n" ; */

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<!--
 Red Dog Bus Company Online ticket Purchase System - Mobile
 Version 0.0.0, date/last/modified
 Ed Arvelaez

 Description here
-->

<meta name="description" content="Red Dog Bus Company Online ticket Purchase System" />
<meta name="keywords" content="jquery,mobile" />
<meta name="author" content="Ed Arvelaez" />
<meta http-equiv="content-type"  content="text/html;charset=utf-8" />
<!-- <meta http-equiv="cache-control" CONTENT="no-cache" /> -->
<meta charset="utf-8" />
<meta name="viewport"
      content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no"/>
<meta name="apple-mobile-web-app-capable" content="yes" />

<link media="screen" rel="stylesheet" href="/common/jquery/jquery.mobile-1.4.5.min.css" />
<link media="screen" rel="stylesheet" href="/apps/reddogbuscompany/mobile/style.css" />
<script type="text/javascript" src="/common/jquery/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/common/jquery/jquery.mobile-1.4.5.min.js"></script>
<script type="text/javascript" src="reddogbuscompany.js"></script>

<title>Red Dog Bus Company - Mobile</title>
</head>
<body>
<div data-role="page" id="divPageRouteSelection" data-theme="b">
 <div data-role="header" data-theme="b">
  <h1 id="h1PageRouteSelectionHeader">Red Dog Bus Company</h1>
 </div>
 <div data-role="content">
  <ul id="ulRoutes" data-role="listview">
  </ul>
 </div>
</div>

<div data-role="page" id="divPagePurchase" data-theme="b">
 <div data-role="header" data-theme="b">
  <a href="#divPageRouteSelection"
     data-rel="back" data-icon="back"
     data-transition="slide"
     data-direction="reverse"
     id="ancBackToRouteSelection">Back</a>
  <h1 id="h1PagePurchaseHeader">Red Dog Bus Company</h1>
 </div><!-- /header -->
 <div data-role="content">
  <ul id="ulSelection" data-role="listview" data-inset="true">
   <li>Route: <span id="spanRouteSelected">No route selected</span></li>
   <li>Price per ticket: $<span id="spanTicketPrice">0.00</span></li>
   <li>Tickets Available: <span id="spanTicketsAvailable">0</span></li>
  </ul>
  <form id="formTicketSelection">
   <div class="cssDivTicketSlider">
    <label id="lblTicketSlider" for="inpTicketSlider">Select Tickets To Purchase</label>
    <input type="range" id="inpTicketSlider" name="inpTicketSlider" value="0" min="0" max="60" />
    <ul id="ulPurchaseprice" data-role="listview" data-inset="true">
     <li>Purchase Price : $<span id="spanPurchasePrice">0.00</span></li>
    </ul>
    <a href="#divPageConfirmPurchase"
       class="ui-disabled"
       id="btnPurchase"
       data-role="button"
       data-rel="popup"
       data-position-to="window"
       data-transition="pop">Purchase</a>
   </div>
  </form>
 </div>

 <div data-role="popup"
      data-theme="b"
      data-overlay-theme="a"
      id="divPageConfirmPurchase">
  <div data-role="header" data-theme="b">
   <h1 id="h1PageConfirmPurchaseHeader">Confirm purchase?</h1>
  </div><!-- /header -->
  <div data-role="content">
   Are you sure you'd like to make this purchase?
   <div class="cssDivConfirmPurchase">
    <span id="spanConfirmTickets">-1</span> tickets for $<span id="spanConfirmPrice">-1.00</span>
   </div>
   <div class="cssDivCentered">
    <a href="#divPagePurchase"
       id="ancCancel"
       data-role="button"
       data-mini="true"
       data-inline="true"
       data-rel="back"
       data-theme="a">Cancel</a>
    <a href="#divPageConfirmPurchase"
       id="ancPurchase"
       data-role="button"
       data-mini="true"
       data-inline="true"
       data-transition="slide"
       data-direction="reverse">Purchase</a>
   </div>
  </div>
 </div>

  <div data-role="popup"
      data-theme="b"
      data-overlay-theme="a"
      id="divPagePurchaseComplete">
  <div data-role="header" data-theme="b">
   <h1 id="h1PagePurchaseCompleteHeader">Purchase complete</h1>
  </div><!-- /header -->
  <div data-role="content">
   Purchase complete!
   <div class="cssDivCentered">
    <a href="#divPageRouteSelection"
       id="ancContinue"
       data-role="button"
       data-mini="true"
       data-inline="true"
       data-transition="slide"
       data-direction="reverse">Continue</a>
   </div>
  </div>
 </div>

</div>
</body>
</html>