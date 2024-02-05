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
        $userId = checkAuth($conn);

        if(isset($input['poll_id']) && isset($input['vote_id']) && isset($userId)){
            
            $id = 0;
            $poll_id = $input['poll_id'];
            $candidate_id = $input['vote_id'];
            $user_id = $userId;

            $tstamp = date('Y-m-d H:i:s T');
            
            $create_date = $tstamp;
            $modified_date = $tstamp;
            $delete_date = null;//$tstamp;

            create($conn,$id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
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
    
    function create($conn,$id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'insert into vote(id,poll_id,candidate_id,user_id,create_date,modified_date,delete_date) values(?,?,?,?,?,?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiiisss',$idIn,$poll_idIn,$candidate_idIn,$user_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn);
        
        $idIn = $id;
        $poll_idIn = $poll_id;
        $candidate_idIn = $candidate_id;
        $user_idIn = $user_id;
        $create_dateIn = $create_date;
        $modified_dateIn = $modified_date;
        $delete_dateIn = $delete_date;
        
        $result = $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->id = $id;
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
    