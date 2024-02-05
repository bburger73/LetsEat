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
        
        
        $user_id = checkUserAuth($conn);
      
        if( isset($user_id) &&
            isset($input['pass'])){
            $password = $input['pass'];
            if(updatepassword($conn,$password,$user_id))
            {
                http_response_code(200);
                $hold = new stdClass();
                $hold->message = "Update";
                $hold->password = true;
                echo json_encode($hold);
            }else{
                http_response_code(400);
                $hold = new stdClass();
                $hold->message = "Failed update";
                $hold->password = false;
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

    // the userside and maybe backend for forgot
    function updatepassword($conn,$inPass,$id){
        $query = 'update user_pass set password=? where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$pass,$idIn);
        $pass = password_hash($inPass,PASSWORD_DEFAULT);
        $idIn = $id;
        $result = $stmt->execute();
        $stmt->close();
        if($result)
        {
           return true;
        }else{
            return false;
        }
    }
    