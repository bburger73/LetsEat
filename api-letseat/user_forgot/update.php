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
        
        
       
        if(isset($input['token']) && isset($input['pass'])){
            $token = $input['token'];
            $pass = $input['pass'];

            $user_id = read($conn,$token);
            $pass = updatepassword($conn,$pass,$user_id);
            $success = delete($conn,$token);
            $success = true;
            if($success && $pass)
            {
                isLoggedIn($conn,$user_id);   
            }else{
                http_response_code(404);
                $hold = new stdClass();
                $hold->message = "Failed";
                $hold->user_id = $user_id;
                $hold->passupdated = $pass;
                $hold->success = $success;
                echo json_encode($hold);    
            }
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

    //get the userid
    
function read($conn,$token)
{
    $query = 'select user_id,token,expire_date,modified_date,create_date from user_forgot where expire_date>? and token=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss',$tstamp,$idIn);
    
    $idIn = $token;
    $tstamp = date('Y-m-d H:i:s T');

    $result = $stmt->execute();
    $stmt->bind_result($user_id,$token,$expire_date,$modified_date,$create_date);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->user_id = $user_id;
    $hold->token = $token;
    $hold->expire_date = $expire_date;
    $hold->modified_date = $modified_date;
    $hold->create_date = $create_date;
    
    $stmt->close();
    if($result && $hold->user_id > 0)
    {
        return $hold->user_id;
    }else{
        http_response_code(400);
        $hold->message = "Failed";
        echo json_encode($hold);
        exit();
    }
}


    //update the password
    function updatepassword($conn,$inPass,$id){
        $query = 'update user_pass set password=? where user_id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$pass,$idIn);
        $pass = password_hash($inPass,PASSWORD_DEFAULT);
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
    

    //remove the forgot token
    function update($conn,$token){
        //this should be executed after user executes the recovery
        $query = 'update user_forgot set user_id=0 where token=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s',$tokenIn);
        $tokenIn = $token;
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    function delete($conn,$token){
        //this should be executed after user executes the recovery
        $query = 'delete from user_forgot where token=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s',$tokenIn);
        $tokenIn = $token;
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
function isLoggedIn($conn,$user_id)
{
    $out = new stdClass();
    $query = 'select user_account.user_id,user_account.authority,user_account.name,user_pass.password from user_account join user_pass where user_account.user_id=user_pass.user_id and user_account.delete_date is null and user_account.user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$user_idIn);

    $user_idIn = $user_id;

    $stmt->bind_result($user_id,$auth,$name,$password);
    $result = $stmt->execute();
    $stmt->fetch();
    if($user_id > -1)
    {
        
        $out->id = $user_id;
        $out->auth = $auth;
        $out->name = $name;
        $stmt->close();
        
        $tok = readTokenFull($conn,$user_id);
        $out->user_token = $tok->token;

        if($result && !is_null($out->user_token))
        {
            $out->response = "success";
            $out->result = true;
            echo json_encode($out);
        }else{
            // $out = new stdClass();
            $out->id = "failed";
            $out->auth = "failed";
            $out->response = "failed";
            echo json_encode($out);
        }
    }else{
        $out = new stdClass();
        $out->id = "failed";
        $out->auth = "failed";
        $out->response = "failed";
        echo json_encode($out);
    }
}


function generateToken($conn,$user_id)
{
    $query = "update passwords set token=? where user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si',$token,$user);
    $user = $user_id;
    $token = genTok($user_id);
    $result = $stmt->execute();
    
    $stmt->close();
    if($result)
    {
        return $token;
    }else{
        return false;   
    }
}

function genTok($user_id)
{
    $start = date('Y-m-d H:i:s T');
    $out = $user_id . password_hash($start, PASSWORD_DEFAULT);
    return $out;
}



function readTokenFull($conn,$user_id)
{
    $query = 'select user_id,token,reset_token,create_date,expire_date,modified_date from user_token where user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$idIn);
    
    $idIn = $user_id;

    $result = $stmt->execute();
    $stmt->bind_result($user_id,$token,$resettoken,$create_date,$expire_date,$modified_date);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->user_id = $user_id;
    $hold->token = $token;
    $hold->resettoken = $resettoken;
    $hold->expdate = $expire_date;
    $hold->moddate = $modified_date;
    $hold->crdate = $create_date;
    
    $stmt->close();
    if($result && !is_null($hold->token))
    {
        // http_response_code(200);
        return $hold;
    }elseif($result){
        return createSigninToken($conn,$user_id);
    }else{
        return null;
    }
}


function createSigninToken($conn,$user_id)
{
    $hold = new stdClass();
    $query = 'insert into user_token(user_id,token,reset_token,create_date,expire_date,modified_date ) values(?,?,?,?,?,?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssss',$user_idIn,$token,$resettoken,$crdate,$expdate,$moddate);
    
    $user_idIn = $user_id;
    $token = createToken($conn);
    $resettoken = createResetToken($conn);
    $expdate = date('Y-m-d H:i:s T', strtotime("+1 months", strtotime(date('Y-m-d H:i:s T'))));
    $moddate = date('Y-m-d H:i:s T');
    $crdate = date('Y-m-d H:i:s T');

    $hold->user_id = $user_idIn;
    $hold->token = $token;
    $hold->resettoken = $resettoken;
    $hold->expdate = $expdate;
    $hold->moddate = $moddate;
    $hold->crdate = $crdate;
    
    $result = $stmt->execute();
    $stmt->close();
    if($result)
    {
        return $hold;
    }else{
        
    }
}



function createResetToken($conn)
{
    $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    // check if token already exists
    while(readToken($conn,$token))
    {
        $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    }
    return $token;
}

function createToken($conn)
{
    $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    // check if token already exists
    while(readUpdate($conn,$token))
    {
        $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    }
    return $token;
}


function readToken($conn,$user_id)
{
    $query = 'select user_id from user_token where token=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$idIn);
    
    $idIn = $user_id;

    $result = $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->id = $id;
    
    $stmt->close();
    if($result)
    {
        return ($hold->id > 0);
    }else{
        return false;
    }
}