<?php


try {
    include(dirname(__FILE__) . '/../../config/config.inc.php');
	include(dirname(__FILE__) . '/invoicebox.php');
    $invoicebox = new Invoicebox();
    Tools::safePostVars();
	$orderId = Order::getOrderByCartId((int)$_POST['participantOrderId']);
    $order = new Order($orderId);
	file_put_contents(dirname(__FILE__).'/log.txt' , print_r($orderId,1), FILE_APPEND);
    if (!$order) {
        die('NOTOK');
    }
	$participantId= trim($_POST["participantId"]);
    $participantOrderId= trim($_POST["participantOrderId"]);
    $ucode 		= trim($_POST["ucode"]);
	$timetype 	= trim($_POST["timetype"]);
	$time 		= str_replace(' ','+',trim($_POST["time"]));
	$amount 	= trim($_POST["amount"]);
	$currency 	= trim($_POST["currency"]);
	$agentName 	= trim(html_entity_decode($_POST["agentName"], ENT_QUOTES, 'UTF-8'));
	$agentPointName = trim(html_entity_decode($_POST["agentPointName"], ENT_QUOTES, 'UTF-8'));
	$testMode 	= trim($_POST["testMode"]);
	$sign	 	= trim($_POST["sign"]);
	$participant_apikey 	=  $invoicebox->invoicebox_api_key;
		$sign_strA = 
			$participantId .
			$participantOrderId .
			$ucode .
			$timetype .
			$time .
			$amount .
			$currency .
			$agentName .
			$agentPointName .
			$testMode .
			$participant_apikey;
		$sign_crcA = md5( $sign_strA );

	if ( strtolower($sign_crcA) != strtolower($sign) )
		{
			die( "NOTOK SIGN" );
		}; 
    
    $amount_order = number_format($order->total_paid, 2, '.', '');
	if ( $amount_order != $amount)
		{
			die( "NOTOK SUMM" );
		}; 
    $newStatus = Configuration::get('PS_OS_PAYMENT');
 
    $order->setCurrentState($newStatus);
    die('OK');
} catch (Exception $e) {
    die('NOTOK');
}