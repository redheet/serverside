<?php

$server_key = "SB-Mid-server-Ve6Mz0sXRqkN41yRDd0d91V7"; //testing
// $server_key = "Mid-server-s88bHHZB2TxMQFH7cD8-erEq"; //production

// if transaction using app.midtrans for production
// if transaction using app.sandbox for testing 

$is_production = false; //testing
// $is_production = true; //production
$api_url = $is_production ?
	'https://app.midtrans.com/snap/v1/transactions' :
	'https://app.sandbox.midtrans.com/snap/v1/transactions';

if(!strpos($_SERVER['REQUEST_URI'], '/charge')){
	http_response_code(404);
	echo "WRONG PATH, MAKE SURE IT'S `/charge` ";
	exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(404);
	echo "PAGE NOT FOUND OR WRONG HTTP REQUEST METHOD IS USED";
	exit();
}

$request_body = file_get_contents('php://input');
header('Content-Type: application/json');

$charge_result = chargeAPI($api_url, $server_key, $request_body);

http_response_code($charge_result['http_code']);

echo $charge_result['body'];

function chargeAPI($api_url, $server_key, $request_body)
{
	$ch = curl_init();
	$curl_options =  array(
		CURLOPT_URL => $api_url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		//adding header, for response key server
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic ' . base64_encode($server_key. ':'))
		,
		CURLOPT_POSTFIELDS => $request_body
	);

	curl_setopt_array($ch, $curl_options);
	$result = array(
		'body' => curl_exec($ch),
		'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
	);

	return $result;
}
?>
