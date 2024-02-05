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

        if(isset($user_id) && isset($input['password'])){
            
            $password = $input['password'];

            create($conn,$user_id,$password);
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
    

    // should only be used on initiall signup
    function create($conn,$user_id,$password)
    {
        $query = 'insert into passwords(id,user_id,password) values(0,?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is',$user_idIn,$passwordIn);
        
        $user_idIn = $user_id;
        $passwordIn = $password;
        
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
    