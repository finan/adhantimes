<?php

// Parameters
$hubVerifyToken = 'adhan_times_bot';
$accessToken = "EAAZAMQNZBVw5EBADhdApAW9buiXB6IE53Fh0cNsnXASsZACUuHKI5nBmUTJabkntP5yfZAXv2XAjXwbyYlZAeLRbF6YXIUQbdecWfbdCuYc0Nis601Pl5loIjopMF9ojCQlUXPbraJQMS86wuvTlIOlV6dxuWXaVB0CWAiPD1AAZDZD";

// Check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

// Handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$googleApiKey = 'AIzaSyClPZXMggSWOc9fYvs8z_DTv23lyWuYrmE';

if (substr( $messageText, 0, 1 ) === "@")
{
  $address = str_replace("@", "", $messageText); 
} 
else
{
  $address = false;
}

// <--- Google Maps Geocoding API --->

$ch = curl_init();

$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.$googleApiKey;

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$result = curl_exec($ch);
curl_close($ch);

$json = json_decode($result, true);

$lat = $json['results'][0]['geometry']['location']['lat'];
$lng = $json['results'][0]['geometry']['location']['lng'];

// <--- Google Maps Time Zone API --->

$ch = curl_init();

$timestamp = time();

$url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$lng.'&timestamp='.$timestamp.'&key='.$googleApiKey;

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$result = curl_exec($ch);

curl_close($ch);

$json = json_decode($result, true);

$timeZoneId = $json['timeZoneId'];

// <--- Prayer Times API --->

$ch = curl_init();

$url = 'http://api.aladhan.com/timings/'.$timestamp.'?latitude='.$lat.'&longitude='.$lng.'&timezonestring='.$timeZoneId.'&method=3';

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$result = curl_exec($ch);

curl_close($ch);

$json = json_decode($result, true);

$date = $json['data']['date']['readable'];
$fajr = $json['data']['timings']['Fajr'];
$sunrise = $json['data']['timings']['Sunrise'];
$dhuhr = $json['data']['timings']['Dhuhr'];
$asr = $json['data']['timings']['Asr'];
$sunset = $json['data']['timings']['Sunset'];
$maghrib = $json['data']['timings']['Maghrib'];
$isha = $json['data']['timings']['Isha'];
$imsak = $json['data']['timings']['Imsak'];
$midnight = $json['data']['timings']['Midnight'];

// <--- Kondisi --->

if (substr( $messageText, 0, 1 ) === "@")
{
	$answer = 'Date: '.$date.'<br>Fajr: '.$fajr.'<br>Sunrise: '.$sunrise.'<br>Dhuhr: '.$dhuhr.'<br>Asr: '.$asr.'<br>Sunset: '.$sunset.'<br>Maghrib: '.$maghrib.'<br>Isha: '.$isha.'<br>Imsak: '.$imsak.'<br>Midnight: '.$midnight.'<br>';
}

elseif (!empty($messageText))

{
	$answer = 'As-salāmu ʿalaykum Brother, Sister. To get prayer times schedule please type your current address, begins with "@" symbol. For example "@Santa Clara", or "@1 Hacker Way. Menlo Park, CA 94025".';
}

$response = [
	'recipient' => [ 'id' => $senderId ],
	'message' => [ 'text' => $answer ]
];
	
$ch = curl_init();

$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken;

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);

?>