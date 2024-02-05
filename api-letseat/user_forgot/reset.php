<?php
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: *');

    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        if(isset($input['token']) and isset($input['password']))
        {
            $token = validate_input($input['token']);
            $pass = validate_input($input['password']);
            update($conn,$token,$pass);
        }else{
            http_response_code(422);
            $hold = new stdClass();
            $hold->message = "Failed";
            echo json_encode($hold);    
        }
    }elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        
        http_response_code(200);
        $hold = new stdClass();
        $hold->msg = "success";
        echo json_encode($hold);
    }else{
        http_response_code(405);
        $hold = new stdClass();
        $hold->message = "Failed";
        echo json_encode($hold);
    }

    function update($conn,$token,$pass)
    {
        $query = 'update user_account join user_forgot join user_pass set user_pass.token=? where user_account.email=user_forgot.email and user_account.user_id=user_pass.user_id and token=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss',$passwordIn,$tokenIn);
        
        $tokenIn = $token;
        $passwordIn = password_hash($pass,PASSWORD_DEFAULT);
        
        $result = $stmt->execute();
        
        $id = $stmt->id;
        $stmt->close();
        if(deleteToken($conn,$token))
        {
            if($result and $id > 0)
            {
                $hold = new stdClass();
                $hold->result = $result;
                $hold->success = "success";
                echo json_encode($hold);
            }elseif($result and $id <= 0)
            {
                http_response_code(401);
                $hold = new stdClass();
                $hold->result = $result;
                $hold->success = "Faled";
                echo json_encode($hold);
                die();
            }else{
                http_response_code(400);
                $hold = new stdClass();
                $hold->result = $result;
                $hold->success = "failed";
                echo json_encode($hold);
                die();
            }
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->success = "failed";
            echo json_encode($hold);
            die();
        }
    }


    function deleteToken($conn,$token)
    {
        $query = 'delete from user_forgot where token=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s',$tokenIn);
        
        $tokenIn = $token;
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
