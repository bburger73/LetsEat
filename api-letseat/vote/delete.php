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
        $userId = checkAuth($conn);
        if(isset($input['id'])){
            $id = $input['id'];
            delete($conn,$id);
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


    function delete($conn,$idIn)
    {
        $query = 'delete from vote where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$id);
        
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
    