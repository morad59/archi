<?php

//echo "hello world";


//$method = $_SERVER['REQUEST_METHOD'];
//$port = $_SERVER['REMOTE_PORT'];

$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

//var_dump( CallAPI("GET", "http://server1.appliops.com:6081", array("headers" => true)) );  
//echo $method;
echo json_encode($arr); 
//$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5,'f' => get_headers( CallAPI("GET", "http://server1.appliops.com:6081", array("headers" => true)) ) );
//--------------------------------------// 
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
//--------------------------------------// 
/*
function do_something_with_head($req){
  return "head methode";
} 
function do_something_with_put($req){
  return "put methode";
} 
function do_something_with_post($req){
  return "post methode";
} 
function do_something_with_get($req){
  return (string)"get methode";
} 
function handle_error($req){
  return "erreur methode";
} 

switch ($method) {
  case 'HEAD':
    do_something_with_head($req);  
    break;
  case 'PUT':
    do_something_with_put($req);  
    break;
  case 'POST':
    do_something_with_post($req);  
    break;
  case 'GET':
    do_something_with_get($req);  
    break;
  default:
    handle_error($req);  
    break;
}
*/

?>
