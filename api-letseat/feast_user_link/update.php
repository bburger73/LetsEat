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
            isset($input['feasts'])){
            $id = $input['id'];
            $feasts = $input['feasts'];
            $val = updatefeast($conn,$id,$feasts);
            $out->feasts = $val;
            $output = $output && $out;
            $someupdates = true;
        }   
        if( isset($input['id']) and
            isset($input['feast_group_id'])){
            $id = $input['id'];
            $feast_group_id = $input['feast_group_id'];
            $val = updatefeast_group_id($conn,$id,$feast_group_id);
            $out->feast_group_id = $val;
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

    function updatefeast($conn,$id,$feasts)
    {
        $out = true;
        $out = $out && deleteFeasts($conn,$id);
        $tstamp = date('Y-m-d H:i:s T');
        // print_r($restaurants);
        foreach($feasts as $feast)
        {
            // print_r($feast);
            if($feast['selected'])
            {
                $out = $out && create($conn,0,$id,$feast['user_id'],$tstamp,$tstamp,null);                
            }
        }
        return $out;
    }

    function deleteFeasts($conn,$idIn)
    {
        $query = 'delete from feast_user_link where feast_group_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$id);
        
        $id = $idIn;
        
        $result = $stmt->execute();
        $stmt->close();
                
        return $result;
    }
    

    function create($conn,$id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'insert into feast_user_link(id,feast_group_id,user_id,create_date,modified_date,delete_date) values(?,?,?,?,?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiisss',$idIn,$feast_group_idIn,$user_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn);
        
        $idIn = $id;
        $feast_group_idIn = $feast_group_id;
        $user_idIn = $user_id;
        $create_dateIn = $create_date;
        $modified_dateIn = $modified_date;
        $delete_dateIn = $delete_date;
        
        $result = $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $result;
    }
    

    function update($conn,$id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date)
    {
        $query = 'update feast_user_link set id=?,feast_group_id=?,user_id=?,create_date=?,modified_date=?,delete_date=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiisssi',$idIn,$feast_group_idIn,$user_idIn,$create_dateIn,$modified_dateIn,$delete_dateIn,$idIn);
        
        $idIn = $id;
        $feast_group_idIn = $feast_group_id;
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
    
    function updatefeast_group_id($conn,$id,$feast_group_id){
        $query = 'update feast_user_link set feast_group_id=? where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii',$feast_group_idIn,$idIn);
        $feast_group_idIn = $feast_group_id;
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
        $query = 'update feast_user_link set user_id=? where id=?';
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
        $query = 'update feast_user_link set create_date=? where id=?';
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
        $query = 'update feast_user_link set modified_date=? where id=?';
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
        $query = 'update feast_user_link set delete_date=? where id=?';
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
    