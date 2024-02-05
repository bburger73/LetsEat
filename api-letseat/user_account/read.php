<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    // Should have pass and user signin
    // Should have token signin
    // Should return all data in one payload for initial app usage
    // main usage is for users to sign in
    
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        if(isset($input['email']) && isset($input['pass']))
        {
            $user = $input['email'];
            $pass = $input['pass'];
            isLoggedIn($conn,$user,$pass);
        }elseif(isset($input['token'])){
            $token = str_replace("\\",'',$input['token']);
            $loggedin = isLoggedInToken($conn,$token);
            if(!$loggedin)
            {
                http_response_code(400);
                $hold = new stdClass();
                $hold->token = $token;
                $hold->message = "Failed to token login";
                echo json_encode($hold);
            }
        }else{
            http_response_code(422);
            $hold = new stdClass();
            $hold->message = "Failed";
            echo json_encode($hold);
        }

        
    }elseif($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        // $inputJSON = file_get_contents('php://input');
        // $input = json_decode($inputJSON, TRUE);
        
        if(isset($_GET['all'])){
            echo readAll($conn);
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

function readTokenFull($conn,$user_id)
{
    $query = 'select user_id,token,reset_token,create_date,expire_date,modified_date from user_token where user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$idIn);
    
    $idIn = $user_id;

    $result = $stmt->execute();
    $stmt->bind_result($user_idOut,$token,$resettoken,$create_date,$expire_date,$modified_date);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->user_id = $user_idOut;
    $hold->token = $token;
    $hold->resettoken = $resettoken;
    $hold->expdate = $expire_date;
    $hold->moddate = $modified_date;
    $hold->crdate = $create_date;
    
    $stmt->close();
    if($result && !is_null($hold->token))
    {
        return $hold;
    }else{
        return createSigninToken($conn,$user_id);
    }
}


function createSigninToken($conn,$user_id)
{
    $tokenIn = createToken($conn);
    $resetTokenIn = createResetToken($conn);
    $hold = new stdClass();
    $query = 'insert into user_token(user_id,token,reset_token,create_date,expire_date,modified_date ) values(?,?,?,?,?,?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssss',$user_idIn,$token,$resettoken,$crdate,$expdate,$moddate);
    
    $user_idIn = $user_id;
    $token = $tokenIn;
    $resettoken = $resetTokenIn;
    $expdate = date('Y-m-d H:i:s T', strtotime("+1 months", strtotime(date('Y-m-d H:i:s T'))));
    $moddate = date('Y-m-d H:i:s T');
    $crdate = date('Y-m-d H:i:s T');
    $result = $stmt->execute();

    $hold->user_id = $user_idIn;
    $hold->token = $token;
    $hold->resettoken = $resettoken;
    $hold->expdate = $expdate;
    $hold->moddate = $moddate;
    $hold->crdate = $crdate;
    
    $stmt->close();
    if($result)
    {
        return $hold;
    }else{
        return false;
    }
}

function readuser_id($conn,$user_id)
{
    $query = 'select id from user_token where user_id=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$idIn);
    
    $idIn = $user_id;

    $result = $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->id = $id;
    
    $stmt->close();
    if($result)
    {
        return !($hold->id > 0);
    }else{
        return false;
    }
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
function readuser_idFromToken($conn,$token)
{
    $query = 'select user_id from user_token where token=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$idIn);
    
    $idIn = $token;

    $result = $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    
    $hold = new stdClass();
    $hold->id = $id;
    
    $stmt->close();
    if($result)
    {
        return $hold->id;
    }else{
        return false;
    }
}


function readUpdate($conn,$user_id)
{
    $query = 'select user_id from user_token where reset_token=?';
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

function readAll($conn)
{
    $query = 'select user_id,email from user_account';
    $stmt = $conn->prepare($query);
    
    $result = $stmt->execute();
    $stmt->bind_result($user_id,$email);
    $out = array();
    while($stmt->fetch())
    {
        $hold = new stdClass();
        $hold->user_id = $user_id;
        $hold->email = $email;
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



    function read($conn,$id)
    {
        $query = 'select id,user_id,email,authority,school,grade from users where id=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$idIn);
        
        $idIn = $id;
    
        $result = $stmt->execute();
        $stmt->bind_result($id,$user_id,$email,$authority,$school,$grade);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->id = $id;
        $hold->user_id = $user_id;
        $hold->email = $email;
        $hold->authority = $authority;
        $hold->school = $school;
        $hold->grade = $grade;
        
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
    
function getUserAccountData($conn,$user_id)
{
    // $conn->query("create table user_token(id int not null primary key auto_increment,user_id int,token text,resettoken text,expdate text,moddate text,crdate text)");
    $query = 'select names.user_id,name,points,ethnicity,prize_id from names join prize_select where names.user_id=? and names.user_id=prize_select.user_id';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i',$user);
    $user = $user_id;
    $stmt->bind_result($id,$name,$points,$ethnicity,$prize);
    $result = $stmt->execute();
    $stmt->fetch();
    $out = new stdClass();
    $out->id = $id;
    $out->name = $name;
    $out->points = $points;
    $out->ethnicity = $ethnicity;
    $out->prize = $prize;
    if($result)
    {
        return json_encode($out);
    }else{
        
    }
}




/*
    Must update password by adding token,resettoken,expiredate,setdate,updatedate
*/
function isLoggedInToken($conn,$tokenIn)
{
    // $conn->query('user involved_fall2021_userdata');
    $out = new stdClass();
    $query = 'select user_account.user_id,user_account.authority,user_account.name from user_account join user_token where user_account.user_id=user_token.user_id and user_account.delete_date is null and user_token.token=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$toknIn);

    $toknIn = $tokenIn;

    $stmt->bind_result($user_id,$auth,$name);
    // $stmt->bind_result($user_id,$auth,$points,$prize,$school,$grade,$name,$phone,$email,$token,$user_token);
    $result = $stmt->execute();
    $stmt->fetch();

    $out->id = $user_id;
    $out->auth = $auth;
    $out->name = $name;
    
    $stmt->close();
    
    if($result && $out->id > 0)// and !strcmp($name,''))
    {
        $out->response = "success";
        $out->user_token = $tokenIn;
        echo json_encode($out);
        exit();
    }else{
        return false;
    }
}


function isLoggedIn($conn,$user,$pass)
{
    $out = new stdClass();
    $query = 'select user_account.user_id,user_account.authority,user_account.name,user_pass.password from user_account join user_pass where user_account.user_id=user_pass.user_id and user_account.delete_date is null and user_account.email=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$emailIn);

    $emailIn = $user;

    $stmt->bind_result($user_id,$auth,$name,$password);
    $result = $stmt->execute();
    $stmt->fetch();
    if($user_id > -1)
    {
        
        $out->id = $user_id;
        $out->auth = $auth;
        $out->name = $name;
        $out->password = $password;
        $stmt->close();
        
        $tok = readTokenFull($conn,$out->id);
        $out->user_token = $tok->token;

        $passwordv = password_verify($pass,$out->password);
        if($result && $passwordv && !is_null($out->user_token))
        {
            $out->response = "success";
            echo json_encode($out);
        }else{
            // $out = new stdClass();
            $out->passwordv = $passwordv;
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
