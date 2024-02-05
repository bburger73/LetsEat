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
            isset($input['candidates'])){
            $id = $input['id'];
            $candidates = $input['candidates'];
            $val = updatecandidates($conn,$id,$candidates);
            $out->candidates = $val;
            $output = $output && $out;
            $someupdates = true;
        }  
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
            isset($input['restaurant_id'])){
            $id = $input['id'];
            $restaurant_id = $input['restaurant_id'];
            $val = updaterestaurant_id($conn,$id,$restaurant_id);
            $out->restaurant_id = $val;
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

    function updatecandidates($conn,$id,$candidates)
    {
        $out = true;
        $out = $out && deletecandidates($conn,$id);
        $tstamp = date('Y-m-d H:i:s T');
        // print_r($candidates);
        foreach($candidates as $candidate)
        {
            if($candidate['selected'])
            {
                $out = $out && create($conn,0,$id,$candidate['id'],$tstamp,$tstamp,null);                
            }
        }
        return $out;
    }

    function deletecandidates($conn,$idIn)
    {
        $query = 'delete from candidate where poll_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$id);
        
        $id = $idIn;
        
        $result = $stmt->execute();
        $stmt->close();
                
        return $result;
    }
    
    function create($conn,$id,$poll_id,$restaurant_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'insert into candidate(id,poll_id,restaurant_id,create_date,modified_date,delete_date) values(?,?,?,?,?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiisss',$idIn,$poll_idIn,$restaurant_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn);
        
        $idIn = $id;
        $poll_idIn = $poll_id;
        $restaurant_idIn = $restaurant_id;
        $create_dateIn = $create_date;
        $modified_dateIn = $modified_date;
        $delete_dateIn = $delete_date;
        
        $result = $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $result;
    }
    

    function update($conn,$id,$poll_id,$restaurant_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'update candidate set id=?,poll_id=?,restaurant_id=?,create_date=?,modified_date=?,delete_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiisssi',$idIn,$poll_idIn,$restaurant_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn,$idIn);
        
        $idIn = $id;
        $poll_idIn = $poll_id;
        $restaurant_idIn = $restaurant_id;
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
        $query = 'update candidate set poll_id=? where id=?';
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
    
    function updaterestaurant_id($conn,$id,$restaurant_id){
        $query = 'update candidate set restaurant_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$restaurant_idIn,$idIn);
        $restaurant_idIn = $restaurant_id;
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
        $query = 'update candidate set create_date=? where id=?';
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
        $query = 'update candidate set modified_date=? where id=?';
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
        $query = 'update candidate set delete_date=? where id=?';
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
    