<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        $user_id = checkUserAuth($conn);

        if(isset($user_id)){
            echo read($conn,$user_id);
        }else{
            // echo readAll($conn);
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

    // only used by server to send stuff
    function read($conn,$user_id)
    {
        $query = 'select user_id,token from user_push where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$user_idIn);
        
        $user_idIn = $user_id;
    
        $result = $stmt->execute();
        $stmt->bind_result($user_id,$token);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->user_id = $user_id;
        $hold->token = $token;
        
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            return json_encode($hold);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }

    // only used by bulk send?
    function readAll($conn)
    {
        $query = 'select * from user_push';
        $stmt = $conn->prepare($query);        
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($user_id,$token);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->user_id = $user_id;
            $hold->token = $token;
            
            $out[] = $hold;
        }
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            return json_encode($out);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }
    