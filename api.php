<?php
header("Content-Type:application/json");

if (!empty($_GET['name'])) {
	$name = $_GET['name'];
	response(200, "Van név", NULL);
} else {
	response(400, "Invalid Request", NULL);
}

function response($status, $status_message, $data)
{
	header("HTTP/1.1 " . $status);

	$response['status'] = $status;
	$response['status_message'] = $status_message;
	$response['data'] = $data;

	$json_response = json_encode($response);
	echo $json_response;
}
