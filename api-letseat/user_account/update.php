<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");


    // Should update all account information...
    // May need to reintegrate password into this form


    if($_SERVER['REQUEST_METHOD'] === 'PUT')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        if( isset($input['email']) || 
            isset($input['name']) ||
            isset($input['notification'])
        ){
            $user_id = checkUserAuth($conn);
            $bool = true;
            $out = new stdClass();
            if( isset($user_id) &&
                isset($input['email']) &&
                $input['email'] != "" &&
                $input['email'] != ''){
                $email = $input['email'];
                $out->email = updateemail($conn,$email,$user_id);
                $bool = $bool && $out->email;
            }

            if( isset($user_id) &&
                isset($input['name']) &&
                $input['name'] != "" &&
                $input['name'] != ''){
                $name = $input['name'];
                $out->name = updatename($conn,$name,$user_id);
                $bool = $bool && $out->name;
            }

            if( isset($user_id) &&
                isset($input['notification']) &&
                $input['notification'] != "" &&
                $input['notification'] != '')
            {
                $notificationType = $input['notification'];
                $out->notification = updatenotificationType($conn,$notificationType,$user_id);
                $bool = $bool && $out->notification;
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

    
    
    function updateemail($conn,$email,$id){
        $query = 'update user_account set email=? where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$emailIn,$idIn);
        $emailIn = $email;
        $idIn = $id;
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    

function updatename($conn,$name,$user_id)
{
    $query = 'update user_account set name=? where user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si',$nm,$id);
    $nm = $name;
    $id = $user_id;
    $result = $stmt->execute();
    return $result;
}

function updatenotificationType($conn,$notificationType,$user_id){
    $query = 'update user_account set notification=? where user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii',$notificationTypeIn,$user_idIn);
    $notificationTypeIn = (int)$notificationType + 0;
    $user_idIn = (int)$user_id + 0;
    $result = $stmt->execute();
    $stmt->close();
    if($result)
    {
       return true;
    }else{
        return false;
    }
}
