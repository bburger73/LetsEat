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
            echo read($conn,$id);
        }elseif(isset($_GET['feast_group_id'])){
            $id = $_GET['feast_group_id'];//$_GET['id'];
            echo readGroup($conn,$id);
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

    function readGroup($conn,$id)
    {
        $query = 'select id,feast_group_id,user_id,create_date,modified_date,delete_date from feast_user_link where feast_group_id=?';
        $stmt = $conn->prepare($query);  
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
          
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $out[] = $user_id;
            // $hold = new stdClass();
            // $hold->id = $id;
            // $hold->feast_group_id = $feast_group_id;
            // $hold->user_id = $user_id;
            // $hold->create_date = $create_date;
            // $hold->modified_date = $modified_date;
            // $hold->delete_date = $delete_date;
            
            // $out[] = $hold;
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

    function read($conn,$id)
    {
        $query = 'select id,feast_group_id,user_id,create_date,modified_date,delete_date from feast_user_link where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->result = $result;
        $hold->id = $id;
        $hold->feast_group_id = $feast_group_id;
        $hold->user_id = $user_id;
        $hold->create_date = $create_date;
        $hold->modified_date = $modified_date;
        $hold->delete_date = $delete_date;
        
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            return json_encode($hold);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            return json_encode($hold);
        }
    }

    function readAll($conn)
    {
        $query = 'select * from feast_user_link';
        $stmt = $conn->prepare($query);        
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$feast_group_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->id = $id;
            $hold->feast_group_id = $feast_group_id;
            $hold->user_id = $user_id;
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
    