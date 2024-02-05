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
            // echo read($conn,$user_id);
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

    // should never be used
    function read($conn,$id)
    {
        $query = 'select id,user_id,password from passwords where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$user_id,$password);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->id = $id;
        $hold->user_id = $user_id;
        $hold->password = $password;
        
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

    // should not exist
    function readAll($conn)
    {
        $query = 'select * from passwords';
        $stmt = $conn->prepare($query);        
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$user_id,$password);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->id = $id;
            $hold->user_id = $user_id;
            $hold->password = $password;
            
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
    