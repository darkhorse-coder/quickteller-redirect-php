<?php

$project='eatngo-africa';
$key='payment_server_key';
$db_connection = pg_connect("host='localhost' dbname='db_name' user='db_username' password='db_password'");

//echo $_REQUEST['resp'];
if($_REQUEST['resp']=='00')
	{
		$transactionId=$_REQUEST['payRef'];
		$id=$_REQUEST['txnref'];
		$query = 'UPDATE w_payment_transactions SET transaction_result = $1 WHERE transaction_code = $2';
		pg_prepare($db_connection,'sql2',$query);
		pg_execute($db_connection,'sql2', array($transactionId, $id));
		
		pg_prepare($db_connection,'sqlstatus_check', 'SELECT * FROM w_payment_transactions WHERE transaction_code = $1');
		$resultchk = pg_execute($db_connection,'sqlstatus_check',array($id));
		if (pg_num_rows($resultchk) > 0){
			$rowsq = pg_fetch_array($resultchk);
			$order_data = json_decode($rowsq["order_data"]);
			if($order_data!=''){
				$paymethod_id=$order_data->paymethod_id;
				$business_id=$order_data->business_id;
				$delivery_type=$order_data->delivery_type;
				$driver_tip=$order_data->driver_tip;
				$delivery_zone_id=$order_data->delivery_zone_id;
				$location=$order_data->location;
				$products=$order_data->products;
				$customer_id=$order_data->customer_id;
				$customer=$order_data->customer;
				$delivery_datetime=$order_data->delivery_datetime;
				if($delivery_datetime==''){
					$delivery_datetime=date("Y-m-d H:i:s");
				}
				
				$postfields1 = array(
					"business_id" => $business_id,
					"paymethod_id" => $paymethod_id,
					"customer_id" => $customer_id,
					"delivery_type" => $delivery_type,
					"location" => $location,
					"products" => $products,
					"customer" => $customer,
					"delivery_datetime" => $delivery_datetime,
					"delivery_zone_id" => $delivery_zone_id,
					"pay_data" => 'success',
					"driver_tip" => $driver_tip
				);
				$data_json1 = json_encode($postfields1);
				
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://apiv4.ordering.co/v400/en/'.$project.'/orders');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json1);

				$headers = array();
				$headers[] = 'X-Api-Key: '.$key;
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'X-App-X: web';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				$result1 = json_decode($result);
				if (curl_errno($ch)) {
					//echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);
				//print_r($result);
				echo $result1->result->id;
				include_once('../../socket/client.php');
				$ws = new ws(array('host' => '34.198.82.86', 'port' => 8080, 'path' => ''));
					$position = array(
						"transaction_id" => $transactionId,
						"order_id" => $result1->result->id,
						"order" => $result
						
					);
				$submessage = array('handle' => 'order', 'data' => json_encode($position));
				$message = array('handle' => 'internal', 'room' => 'interswitch_'.$id, 'data' => json_encode($submessage));
				$result_socket = $ws->send(json_encode($message));
			}
		}
		
	}else{
		$transactionId='failed';
		$id=$_REQUEST['txnref'];
		$query = 'UPDATE w_payment_transactions SET transaction_result = $1 WHERE transaction_code = $2';
		pg_prepare($db_connection,'sql2',$query);
		pg_execute($db_connection,'sql2', array($transactionId, $id));
		include_once('../../socket/client.php');
		$ws = new ws(array('host' => '34.198.82.86', 'port' => 8080, 'path' => ''));
			$position = array(
				"transaction_id" => ''			
			);
		$submessage = array('handle' => 'order', 'data' => json_encode($position));
		$message = array('handle' => 'internal', 'room' => 'interswitch_'.$id, 'data' => json_encode($submessage));
		$result_socket = $ws->send(json_encode($message));
	}
?>
