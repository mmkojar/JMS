<?php

function sendSMS($msg,$mobile)
{
	$user = 'Esakpiy';
	$pass = '123456';
	$senderId = 'PKCGUJ';
	$message = urlencode($msg);
	
	$hit_url = "http://login.businesslead.co.in/api/mt/SendSMS?user=".$user."&password=".$pass."&senderid=".$senderId."&channel=Trans&DCS=0&flashsms=0&number=".$mobile."&text=".$message."&route=6";	
	$url = $hit_url; 

	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$buffer = curl_exec($curl);

	curl_close($curl);
	return $buffer;

}
 
?>