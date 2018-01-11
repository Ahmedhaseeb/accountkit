<?php 

error_reporting(0);
//Put your fb accountkit app id and accountkit app secret below
define( "FB_ACCOUNT_KIT_APP_ID", "FB_ACCOUNT_KIT_APP_ID"); // facebook ACCOUNT_KIT_APP_ID here
define( "FB_ACCOUNT_KIT_APP_SECRET", "FB_ACCOUNT_KIT_APP_SECRET"); // FB_ACCOUNT_KIT_APP_SECRET
$code = $_POST['code'];
$csrf = $_POST['csrf'];

$auth = file_get_contents( 'https://graph.accountkit.com/v1.1/access_token?grant_type=authorization_code&code='.  $code .'&access_token=AA|'. FB_ACCOUNT_KIT_APP_ID .'|'. FB_ACCOUNT_KIT_APP_SECRET );

$access = json_decode( $auth, true );
if( empty( $access ) || !isset( $access['access_token'] ) ){
	//setting statusCode to 1 for debugging
    echo json_encode( array( "statusCode" => 1, "message" => "Unable to verify the Phone Number/Email." ) );
    return;
}

//App scret proof key Ref : https://developers.facebook.com/docs/graph-api/securing-requests
$appsecret_proof= hash_hmac( 'sha256', $access['access_token'], FB_ACCOUNT_KIT_APP_SECRET ); 

$url = 'https://graph.accountkit.com/v1.1/me/?access_token='. $access['access_token'].'&appsecret_proof='. $appsecret_proof;

$ch = curl_init();
// Set query data here with the URL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$resp = trim(curl_exec($ch));

curl_close($ch);

$info = json_decode( $resp, true );
if( empty( $info ) || ( !isset( $info['phone'] ) AND !isset($info['email']) ) || isset( $info['error'] ) ){
	//setting statusCode to 2 for debugging
    echo json_encode( array( "status" => 2, "message" => "Unable to verify the Phone Number/Email." ) );
    return;
}

if(isset($info['phone'])){
	$phoneNumber = $info['phone']['national_number'];
}elseif(isset($info['email'])){
	$email = $info['email']['address'];
}

echo json_encode( $info );

//Now check user from your database if it exists then login otherwise show message of registation
//Obviously via Facebook Login most simple and easyway


// $user = $this->db->query( "SELECT * FROM user WHERE phone_number = '". $phoneNumber ."'" )->result_array();

// if( !empty( $user ) ){
//     //Create session
//     return array( "status" => "01", "message" => "Login success", "token" => $jwt );
// }else{
//     return array( "status" => "02", "message" => "Phonenumber not registered with us." );
// }


?>