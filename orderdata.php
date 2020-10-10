<?php
	$json = file_get_contents('php://input');
	$obj = json_decode( $json );
	header('Content-Type: application/json; charset=utf-8');
	header("Access-Control-Allow-Origin: *");
	$db_connection = pg_connect("host='localhost' dbname='plugin_deb' user='postgres' password='HappyOrdering2017'");

		//$response['result'] =array();
	$code = $obj->{'orderid'};
	$order = json_encode($obj->{'order'});
	
	$query = 'INSERT INTO w_payment_transactions (transaction_code,order_data) VALUES ($1,$2)';
	pg_prepare($db_connection,'sql2',$query);
	pg_execute($db_connection,'sql2', array($code,$order));
?>