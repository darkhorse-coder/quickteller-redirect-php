<?php

ob_start();
session_start();

$json = file_get_contents('php://input');
$obj = json_decode( $json );
$business_id = $_GET['business'];
$code = $_GET['code'];
$total =str_replace(",", "",$_GET['total']);
$total = round($total,2);
//echo $total;
//$total = number_format((float)$total, 2, '.', '');
$total=$total*100;
$email = $_GET['email'];

$pay_item_id = $_GET['pay_item_id'];
$merchant_code = $_GET['merchant_code'];
$sandbox = $_GET['sandbox'];	
$inv=rand();
if($sandbox==true){
	$url="https://qa.interswitchng.com/collections/w/pay";
}else{
	$url="https://webpay.interswitchng.com/collections/w/pay";

}

$redirectUrl = 'https://plugins-development-2.ordering.co/deb/quickteller/callback.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>interswitch</title>
	</head>  
	<body> 
		<form method='post' action='<?=$url?>' style="margin-left: 28px" id="frm1" name="frm1"> 
		<input type='hidden' name='site_redirect_url' value='<?=$redirectUrl?>' /> 
		<input type='hidden' name='pay_item_id' value='<?=$pay_item_id?>' /> 
		<input type='hidden' name='txn_ref' value='<?=$code?>' /> 
		<input type='hidden' name='amount' value='<?=$total?>' /> 
		<input type='hidden' name='currency' value='566' /> 
		<input type='hidden' name='cust_name' value='Blank Limited' /> 
		<input type='hidden' name='pay_item_name' value='Item A' /> 
		<input type='hidden' name='display_mode' value='PAGE' /> 
		<input type='hidden' name='merchant_code' value='<?=$merchant_code?>' /> 

		</form> 
	</body>
	<script>document.getElementById('frm1').submit();</script>
</html>