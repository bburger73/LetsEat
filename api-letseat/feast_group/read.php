<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $userId = checkAuth($conn);

        if(isset($_GET['id'])){
            $id = $_GET['id'];//$_GET['id'];
            $result = read($conn,$id);
            if($result)
            {
                http_response_code(200);
                return json_encode($result);
            }else{
                http_response_code(400);
                $hold = new stdClass();
                $hold->result = $result;
                $hold->message = "Failed";
                return json_encode($hold);
            }
        }elseif(isset($_GET['mine']) && isset($userId)){
            echo readMine($conn,$userId);
        }elseif(isset($_GET['owner']) && isset($userId)){
            echo readOwner($conn,$userId);
        }else{
            echo readAll($conn);
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

    
    function readMine($conn,$userId)
    {
        $query = 'select id,feast_group_id,user_id,create_date,modified_date,delete_date from feast_user_link where user_id=?';
        $stmt = $conn->prepare($query);  
        $stmt->bind_param('i',$idIn);
        
        $idIn = $userId;
          
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $out[] = $feast_group_id;
        }
        $stmt->close();
        $feast = array();
        foreach($out as $poll)
        {
            $feast[] = read($conn,$poll);
        }
        if($result)
        {
            http_response_code(200);
            return json_encode($feast);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }
    


    function read($conn,$id)
    {
        $query = 'select id,name,owner_id,create_date,modified_date,delete_date from feast_group where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$name,$owner_id,$create_date,$modified_date,$delete_date);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->result = $result;
        $hold->id = $id;
        $hold->name = $name;
        $hold->owner_id = $owner_id;
        $hold->create_date = $create_date;
        $hold->modified_date = $modified_date;
        $hold->delete_date = $delete_date;
        
        $stmt->close();
        if($result)
        {
            return $hold;
        }else{
            return false;
        }
    }

    function readAll($conn)
    {
        $query = 'select * from feast_group';
        $stmt = $conn->prepare($query);        
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$name,$owner_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->id = $id;
            $hold->name = $name;
            $hold->owner_id = $owner_id;
            $hold->create_date = $create_date;
            $hold->modified_date = $modified_date;
            $hold->delete_date = $delete_date;
            
            $out[] = $hold;
        }
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            return json_encode($out);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }
    
    function readOwner($conn,$owner)
    {
        $query = 'select id,name,owner_id,create_date,modified_date,delete_date from feast_group where owner_id=?';
        $stmt = $conn->prepare($query);     
        $stmt->bind_param('i',$idIn);
        
        $idIn = $owner;   
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$name,$owner_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->id = $id;
            $hold->name = $name;
            $hold->owner_id = $owner_id;
            $hold->create_date = $create_date;
            $hold->modified_date = $modified_date;
            $hold->delete_date = $delete_date;
            
            $out[] = $hold;
        }
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            return json_encode($out);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }
    