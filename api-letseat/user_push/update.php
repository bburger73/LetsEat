<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'PUT')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        if(isset($input['token'])) // ors || 
        {
            $user_id = checkUserAuth($conn);
            $bool = true;
            $out = new stdClass();
            if( isset($user_id) &&
                isset($input['token'])){
                $token = $input['token'];
                $out->token = updatetoken($conn,$token,$user_id);
                $bool = $bool && $out->token;
            }
            
            if($bool){
                http_response_code(200);
                $hold = new stdClass();
                $hold->message = "Success";
                $hold->results = $out;
                echo json_encode($hold);
            }else{
                http_response_code(206);
                $hold = new stdClass();
                $hold->message = "Partial";
                $hold->results = $out;
                echo json_encode($hold);
            }
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
    
    // This is used by everyone on signin
    function updatetoken($conn,$token,$user_id){
        $query = 'insert into user_push(user_id,token,create_date,modified_date) values(?,?,?,?) on duplicate key update token=?,modified_date=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isssss',$user_idIn,$tokenIn,$tstamp,$tstamp,$tokenIn,$tstamp);
        $tokenIn = $token;
        $user_idIn = $user_id;
        $tstamp = date('Y-m-d H:i:s T');
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    