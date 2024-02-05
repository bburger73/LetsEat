<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'DELETE')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        
        $user_id = checkUserAuth($conn);

        $id = $input['id'];
        delete($conn,$id);
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

    // should never be used...  maybe soft delete?
    function delete($conn,$idIn)
    {
        $query = 'update user_pass set delete_date=? where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$tstamp,$id);
        
        $tstamp = date('Y-m-d H:i:s T');
        $id = $idIn;
        
        $result = $stmt->execute();
        $stmt->close();
                
        if($result)
        {
            http_response_code(200);
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
    