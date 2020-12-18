<?php

$curl = curl_init();
// sample request, will be implemented on client side
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://qa.interswitchng.com/collections/api/v1/gettransaction.json?merchantcode=MX19828&transactionreference=1692665753&amount=10000",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "api_key: "
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>
