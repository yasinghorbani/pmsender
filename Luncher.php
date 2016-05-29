<?php

define('BOT_TOKEN', '239149379:AAF289hyDF5XfcPrQo-LqkRfbfJ4uk10yXg');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  header("Content-Type: application/json");
  echo json_encode($parameters);
  return true;
}

function exec_curl_request($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }

  return $response;
}

function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return exec_curl_request($handle);
}

function processMessage($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    // incoming text message
    $text = $message['text'];
    $admin = 69367395;
    $matches = explode(' ', $text); 
    $substr = substr($text, 0,7 );
    if (strpos($text, "/start") === 0) {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'سلام,👋😃
برای ساخت ربات پیام رسان خود بر روی توکن ربات خود را ارسال کنید.
@PvResanBot'));
        
    } else if ($matches[0] != ""&& $matches[1]==""&&$chat_id != $admin) {
       
    $url = "http://api.telegram.org/bot".$matches[0]."/getme";
    $json = file_get_contents($url);
    $json_data = json_decode($json, true);
    $id = $chat_id;
    if($json_data["result"]["username"]!=null){
      if(!file_exists($id)){
        
        $aaddd = file_get_contents('tokens.txt');
	    	$aaddd .= $text."
";
    	file_put_contents('tokens.txt',$aaddd);
        
     mkdir($id, 0700);
        file_put_contents($id.'/banlist.txt',"");
        file_put_contents($id.'/pmembers.txt',"");
        file_put_contents($id.'/booleans.txt',"false");
        $phptext = file_get_contents('phptext.txt');
        $phptext = str_replace("**TOKEN**",$text,$phptext);
        $phptext = str_replace("**ADMIN**",$chat_id,$phptext);
        file_put_contents($token.$id.'/pvresan.php',$phptext);
        file_get_contents('https://api.telegram.org/bot'.$text.'/setwebhook?url=');
        file_get_contents('https://api.telegram.org/bot'.$text.'/setwebhook?url=https://s1pvresan-makeitgg.rhcloud.com/'.$chat_id.'/pvresan.php');
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "ربات با موفقیت ثبت شد✌😃
"."@".$json_data["result"]["username"]."
به ربات خود بروید و /start بزنید
از ربات خود لذت ببرید 😉
."));
      }
      else{
         apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "شما قبلا یک ربات ثبت کرده اید  و قادر به ساخت بیشتر نیست 🚫
در صورت تمایل به ساخت ربات های بیشتر به ایدی زیر پیام دهید👇👇
@nawr_i_man_bot"));
      }
    
    }
    else{
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "توکن نا معتبر ❌"));

    }
         
         
        }else if ($matches[0] != ""&&$chat_id == $admin) {
       
    $url = "http://api.telegram.org/bot".$matches[0]."/getme";
    $json = file_get_contents($url);
    $json_data = json_decode($json, true);
    $id = $chat_id.$matches[1];
    if($json_data["result"]["username"]!=null){
      if(!file_exists($id)){
        
        $aaddd = file_get_contents('tokens.txt');
	    	$aaddd .= $text."
";
    	file_put_contents('tokens.txt',$aaddd);
        
     mkdir($id, 0700);
        file_put_contents($id.'/banlist.txt',"");
        file_put_contents($id.'/pmembers.txt',"");
        file_put_contents($id.'/booleans.txt',"false");
        $phptext = file_get_contents('phptext.txt');
        $phptext = str_replace("**TOKEN**",$text,$phptext);
        $phptext = str_replace("**ADMIN**",$id,$phptext);
        file_put_contents($token.$id.'/pvresan.php',$phptext);
        file_get_contents('https://api.telegram.org/bot'.$text.'/setwebhook?url=');
        file_get_contents('https://api.telegram.org/bot'.$text.'/setwebhook?url=https://spamer-narimanmaster.c9users.io/'.$id.'/pvresan.php');
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "ربات با موفقیت ثبت شد✌😃
    ".$json_data["result"]["username"]."
به ربات خود بروید و /start بزنید
از ربات خود لذت ببرید 😉
."));
      }
      else{
         apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "شما قبلا یک ربات ثبت کرده اید  و قادر به ساخت بیشتر نیست 🚫
در صورت تمایل به ساخت ربات های بیشتر به ایدی زیر پیام دهید👇👇
@nawr_i_man_bot"));
      }
    
    }
    else{
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "توکن نا معتبر ❌"));

    }
         
         
        }
        else if ($text == "/bots -" && $chat_id==69367395) {
        $adstext=str_replace("/sendads","",$text);
        file_put_contents('tokens.txt',"");
        $tokens = file_get_contents('tokens.txt');
        $part = explode("\n",$tokens);
       $tcount =  count($part)-1;
        
      apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => $tcount));
        
      
    } else if (strpos($text, "/stop") === 0) {
      // stop now
    } else {
      apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Cool'));
    }
  } else {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
  }
}


define('WEBHOOK_URL', 'https://my-site.example.com/secret-path-for-webhooks/');

if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  exit;
}


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
}
