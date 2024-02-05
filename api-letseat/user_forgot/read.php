<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'GET')
    {   
        
        // $user_id = checkUserAuth($conn);

        if( isset($_GET['token'])){
            $token = str_replace("\\",'',$_GET['token']);
            read($conn,$token);
        }else{
            http_response_code(422);
            $hold = new stdClass();
            $hold->message = "Failed";
            echo json_encode($hold);
        }
    }elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
            http_response_code(200);
            $hold = new stdClass();
            $hold->message = "Success";
            echo json_encode($hold);
    }else{
            http_response_code(405);
            $hold = new stdClass();
            $hold->message = "Failed";
            echo json_encode($hold);
    }

// not really needed
function read($conn,$user_id)
{
    $query = 'select user_id,token,expire_date,modified_date,create_date from user_forgot where expire_date>? and token=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss',$tstamp,$idIn);
    
    $idIn = $user_id;
    $tstamp = date('Y-m-d H:i:s T');

    $result = $stmt->execute();
    $stmt->bind_result($user_id,$token,$expire_date,$modified_date,$create_date);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->user_id = $user_id;
    $hold->token = $token;
    $hold->expire_date = $expire_date;
    $hold->modified_date = $modified_date;
    $hold->create_date = $create_date;
    
    $stmt->close();
    if($result && $hold->user_id > 0)
    {
        http_response_code(200);
        echo json_encode($hold);
    }elseif($result){
        http_response_code(404);
        $hold = new stdClass();
        $hold->result = $result;
        $hold->message = "Failed";
        echo json_encode($hold);
    }else{
        http_response_code(400);
        $hold = new stdClass();
        $hold->result = $result;
        $hold->message = "Failed";
        echo json_encode($hold);
    }
}