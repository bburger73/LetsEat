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
        }elseif(isset($_GET['voted']) && isset($userId)){
            echo readPolls($conn,$userId);
        }elseif(isset($_GET['results']) && isset($userId)){
            echo json_encode(readResults($conn,$userId));
        }elseif(isset($_GET['poll_id'])){
            $poll = $_GET['poll_id'];
            echo json_encode(readPollId($conn,$poll));
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

    function readPollId($conn,$poll){
        $query = 'select id,poll_id,candidate_id,user_id,create_date,modified_date,delete_date from vote where poll_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $poll;      
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->poll_id = $poll_id;
            $hold->candidate_id = $candidate_id;
            $hold->user_id = $user_id;
            $hold->create_date = $create_date;
            $hold->modified_date = $modified_date;
            $hold->delete_date = $delete_date;
            $out[] = $hold;
        }
        $stmt->close();

        $outout = array();
        foreach($out as $obj)
        {
            $obj->name = readName($conn,$obj->candidate_id);
            $outout[] = $obj;
        }

        if($result)
        {
            return $outout;
        }else{
            return false;
        }
    }

    function readName($conn,$candidate_id)
    {
        $query = 'select id,name,location,notes,create_date,modified_date,delete_date from restaurant where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $candidate_id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$name,$location,$notes,$create_date,$modified_date,$delete_date);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->result = $result;
        $hold->id = $id;
        $hold->name = $name;
        $hold->location = $location;
        $hold->notes = $notes;
        $hold->create_date = $create_date;
        $hold->modified_date = $modified_date;
        $hold->delete_date = $delete_date;
        
        $stmt->close();
        if($result)
        {
            return $hold->name;
        }else{
            return false;
        }
    }
    
    //should return all poll_ids that the user is apart of
    function readUser($conn,$userId)
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
            $feast = array_merge($feast,readFeast($conn,$poll));
        }
        if($result)
        {
            return $feast;
        }else{
            return false;
        }
    }
    

    function readFeast($conn,$groupId)
    {
        $query = 'select id,owner_id,feast_group_id,name,notes,create_date,end_date,modified_date,delete_date from poll where feast_group_id=?';
        $stmt = $conn->prepare($query); 
        $stmt->bind_param('i',$idIn);
        
        $idIn = $groupId;       
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$owner_id,$feast_group_id,$name,$notes,$create_date,$end_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $out[] = $id;
        }
        $stmt->close();
        if($result)
        {
            return $out;
        }else{
            return $result;
        }
    }
    function readResults($conn,$userId){
        $polls = readUser($conn,$userId);
        $out = array();
        foreach($polls as $poll)
        {
            $out[] = readPoll($conn,$poll);
        }

        return $out;
    }
    function readPoll($conn,$pollId)
    {
        $query = 'select id,poll_id,candidate_id,user_id,create_date,modified_date,delete_date from vote where poll_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $pollId;      
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $out[] = $poll_id;
        }
        $stmt->close();
        if($result)
        {
            return $out;
        }else{
            return false;
        }
    }
    function readPolls($conn,$user_id)
    {
        $query = 'select id,poll_id,candidate_id,user_id,create_date,modified_date,delete_date from vote where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $user_id;      
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $out[] = $poll_id;
            // $hold = new stdClass();
            // $hold->id = $id;
            // $hold->poll_id = $poll_id;
            // $hold->candidate_id = $candidate_id;
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
        $query = 'select id,poll_id,candidate_id,user_id,create_date,modified_date,delete_date from vote where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->result = $result;
        $hold->id = $id;
        $hold->poll_id = $poll_id;
        $hold->candidate_id = $candidate_id;
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
        $query = 'select * from vote';
        $stmt = $conn->prepare($query);        
        
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result($id,$poll_id,$candidate_id,$user_id,$create_date,$modified_date,$delete_date);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            $hold->id = $id;
            $hold->poll_id = $poll_id;
            $hold->candidate_id = $candidate_id;
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
    