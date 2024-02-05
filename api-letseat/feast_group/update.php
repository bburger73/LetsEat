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
            isset($input['name'])){
            $id = $input['id'];
            $name = $input['name'];
            $val = updatename($conn,$id,$name);
            $out->name = $val;
            $output = $output && $out;
            $someupdates = true;
        }        
        if( isset($input['id']) and
            isset($input['owner_id'])){
            $id = $input['id'];
            $owner_id = $input['owner_id'];
            $val = updateowner_id($conn,$id,$owner_id);
            $out->owner_id = $val;
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

    function update($conn,$id,$name,$owner_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'update feast_group set id=?,name=?,owner_id=?,create_date=?,modified_date=?,delete_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isisssi',$idIn,$nameIn,$owner_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn,$idIn);
        
        $idIn = $id;
        $nameIn = $name;
        $owner_idIn = $owner_id;
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
    
    function updatename($conn,$id,$name){
        $query = 'update feast_group set name=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$nameIn,$idIn);
        $nameIn = $name;
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
    
    function updateowner_id($conn,$id,$owner_id){
        $query = 'update feast_group set owner_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$owner_idIn,$idIn);
        $owner_idIn = $owner_id;
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
        $query = 'update feast_group set create_date=? where id=?';
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
        $query = 'update feast_group set modified_date=? where id=?';
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
        $query = 'update feast_group set delete_date=? where id=?';
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
    