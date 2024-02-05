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
        $userId = checkAuth($conn);
        
        $output = true;
        $out =  new stdClass();
        $someupdates = false;
            
        if( isset($input['id']) and
            isset($input['poll_id'])){
            $id = $input['id'];
            $poll_id = $input['poll_id'];
            $val = updatepoll_id($conn,$id,$poll_id);
            $out->poll_id = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['candidate_id'])){
            $id = $input['id'];
            $candidate_id = $input['candidate_id'];
            $val = updatecandidate_id($conn,$id,$candidate_id);
            $out->candidate_id = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['user_id'])){
            $id = $input['id'];
            $user_id = $input['user_id'];
            $val = updateuser_id($conn,$id,$user_id);
            $out->user_id = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['create_date'])){
            $id = $input['id'];
            $create_date = $input['create_date'];
            $val = updatecreate_date($conn,$id,$create_date);
            $out->create_date = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['modified_date'])){
            $id = $input['id'];
            $modified_date = $input['modified_date'];
            $val = updatemodified_date($conn,$id,$modified_date);
            $out->modified_date = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['delete_date'])){
            $id = $input['id'];
            $delete_date = $input['delete_date'];
            $val = updatedelete_date($conn,$id,$delete_date);
            $out->delete_date = $val;
            $output = $output && $out;
            $someupdates = true;
        }
        if($output){
            http_response_code(200);
            $hold = new stdClass();
            $hold->message = "Success";
            $hold->result = $output;
            $hold->out = $out;
            echo json_encode($hold);
        }elseif($someupdates){
            http_response_code(207);
            $hold = new stdClass();
            $hold->message = "Success";
            $hold->result = $output;
            $hold->partial = true;
            $hold->out = $out;
            echo json_encode($hold);
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

    function update($conn,$id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'update vote set id=?,poll_id=?,candidate_id=?,user_id=?,create_date=?,modified_date=?,delete_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiiisssi',$idIn,$poll_idIn,$candidate_idIn,$user_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn,$idIn);
        
        $idIn = $id;
        $poll_idIn = $poll_id;
        $candidate_idIn = $candidate_id;
        $user_idIn = $user_id;
        $create_dateIn = $create_date;
        $modified_dateIn = $modified_date;
        $delete_dateIn = $delete_date;
        
        $result = $stmt->execute();
        $stmt->close();
        
        if($result)
        {
           return true;
        }else{
            return false;
        }
    }
    
    function updatepoll_id($conn,$id,$poll_id){
        $query = 'update vote set poll_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$poll_idIn,$idIn);
        $poll_idIn = $poll_id;
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
    
    function updatecandidate_id($conn,$id,$candidate_id){
        $query = 'update vote set candidate_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$candidate_idIn,$idIn);
        $candidate_idIn = $candidate_id;
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
    
    function updateuser_id($conn,$id,$user_id){
        $query = 'update vote set user_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$user_idIn,$idIn);
        $user_idIn = $user_id;
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
    
    function updatecreate_date($conn,$id,$create_date){
        $query = 'update vote set create_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$create_dateIn,$idIn);
        $create_dateIn = $create_date;
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
    
    function updatemodified_date($conn,$id,$modified_date){
        $query = 'update vote set modified_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$modified_dateIn,$idIn);
        $modified_dateIn = $modified_date;
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
    
    function updatedelete_date($conn,$id,$delete_date){
        $query = 'update vote set delete_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$delete_dateIn,$idIn);
        $delete_dateIn = $delete_date;
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
    