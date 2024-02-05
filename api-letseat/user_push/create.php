<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        $user_id = checkUserAuth($conn);

        if( isset($user_id) &&
            isset($input['token'])){
            
            $token = $input['token'];

            create($conn,$user_id,$token);
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
    

    //only used on first login on device...
    function create($conn,$user_id,$token)
    {
        $query = 'insert into user_push(user_id,token) values(?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is',$user_idIn,$tokenIn);
        
        $user_idIn = $user_id;
        $tokenIn = $token;
        
        $result = $stmt->execute();
        $stmt->close();
        if($result)
        {
            http_response_code(201);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Success";
            echo json_encode($hold);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            echo json_encode($hold);
        }
    }
    