<?php
// parameters
$hubVerifyToken = 'adhan_times_bot';
$accessToken = "EAAZAMQNZBVw5EBADhdApAW9buiXB6IE53Fh0cNsnXASsZACUuHKI5nBmUTJabkntP5yfZAXv2XAjXwbyYlZAeLRbF6YXIUQbdecWfbdCuYc0Nis601Pl5loIjopMF9ojCQlUXPbraJQMS86wuvTlIOlV6dxuWXaVB0CWAiPD1AAZDZD";

// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);

$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];



if($messageText == "Tokyo" || $messageText == "Jakarta" || $messageText == "London" || $messageText == "Cambridge" || $messageText == "Groningen")
{
	$answer = "Sholat yuk!";
}
elseif (!empty($message)
{
    $$answer = "As-salāmu ʿalaykum Brother, Sister. To get prayer times schedule please type your city. For example 'Tokyo'.";
}

$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);

//based on http://stackoverflow.com/questions/36803518