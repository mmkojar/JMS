<?php 

function sendFCMAndroid($message, $title,array $id, $type) {

	//$id = explode(',', $id);
	
	$url = 'https://fcm.googleapis.com/fcm/send';
	$apiKey = "AIzaSyDGd6vilSnK8HebtagNsifpVP06quByyOQ";
 
	$messageData = array('to' => $id, 'data' => array('body' => $message, 'title' => $title, 'type' => $type));
	//print_r($messageData);
	$priority="high";
	
	$data= $messageData= array(		
		'registration_ids' => $id,
		'priority' => $priority,
		'notification' => $messageData,
		'data' => $messageData
	);

	$headers = array(
		'Authorization:key='.$apiKey,
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	//echo json_encode($data);
	
	$result = curl_exec($ch);    
	//print_r($result);       
	//echo curl_error($ch);
	if ($result === FALSE) {
		return FALSE;
		die('Curl failed: ' . curl_error($ch));
		
	}
	curl_close($ch);
	
	return $result; 
}

/*function sendFCM($message, $title, array $id, $type) {

	//$id = explode(',', $id);
	$url = 'https://fcm.googleapis.com/fcm/send';
	//$apiKey = "AIzaSyBBA69pWLPNbciKWVYYjcrszosAIk3BSdo";
	$apiKey = "AIzaSyA3H41sX6_6huSSqaWqETIEN5mktGz0HZ8";
 
	$messageData = array('body' => $message, 'title' => $title, 'type' => $type,"content_available" => true, "mutable_content" => true);
	//print_r($messageData);
	$priority="high";
	
	$data= $messageData= array(
		'notification' => $messageData,
		'priority' => $priority,
		'registration_ids' => $id
	);

	$headers = array(
		'Authorization:key='.$apiKey,
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	//echo json_encode($data);
	
	$result = curl_exec($ch);    
	//print_r($result);       
	//echo curl_error($ch);
	if ($result === FALSE) {
		return FALSE;
		die('Curl failed: ' . curl_error($ch));
		
	}
	curl_close($ch);
	
	return $result; 
}*/

?>