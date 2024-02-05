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
        
        // $user_id = checkUserAuth($conn);

        $hold = new stdClass();
        if( isset($input['email']))
        {
            $user_id = getUserId($conn,$input['email']);
            if(is_int($user_id))
            {
                $hold->create = create($conn,$user_id);
                $hold->message = "Success";
                echo json_encode($hold);                
            }else{
                http_response_code(400);
                $hold = new stdClass();
                $hold->message = "Failed";
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

function create($conn,$user_id)
{
    $createdToken = createToken($conn);
    $query = 'insert into user_forgot(user_id,token,expire_date,modified_date,create_date) values(?,?,?,?,?) on duplicate key update token=?,expire_date=?,modified_date=?,delete_date=NULL';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssssss',$user_idIn,$token,$expdate,$moddate,$crdate,$token,$expdate,$moddate);
    
    $user_idIn = $user_id;
    $token = $createdToken;
    $expdate = date('Y-m-d H:i:s T', strtotime("+1 months", strtotime(date('Y-m-d H:i:s T'))));
    $moddate = date('Y-m-d H:i:s T');
    $crdate = date('Y-m-d H:i:s T');
    
    $result = $stmt->execute();
    $stmt->close();
    if($result)
    {
        return $createdToken;
    }else{
        return false;
    }
}

function readToken($conn,$user_id)
{
    $query = 'select user_id from user_forgot where token=?';
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

function createToken($conn)
{
    $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    // check if token already exists
    while(readToken($conn,$token))
    {
        $token = password_hash(date('Y-m-d H:i:s:u T'), PASSWORD_DEFAULT);
    }
    return $token;
}

function getUserId($conn,$email)
{
    $query = 'select user_id from user_account where email=?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$emailIn);
    
    $emailIn = $email;

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


function sendReset($email,$token)
{
    global $url;
    $newUrl = $url . "/forgot/recover.html?token=" . $token;
    $headers = 'From: AccountRecovery@' . $url . "\r\n" .
    'Reply-To:AccountRecovery@' . $url . "\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=ISO-8859-1\r\n" .
    'X-Mailer: PHP/' . phpversion();
    $msg = "Hello, <a href='$newUrl'>This</a> is a link to reset your account password.\r\n\r\n$newUrl\r\n\r\nIf you believe you have recieved this email in error, please log into your account and change your password.";
    
    if (mail($email, "Password Reset", $msg, $headers)) {
        $hold = new stdClass();
        $hold->success = true;
        $hold->link = "https://$url/forgot/recover.html?token=$token";
        $hold->email = $email;
        $hold->message = "Email Sent!";
        echo json_encode($hold);
    } else {
        $hold = new stdClass();
        $hold->success = false;
        $hold->message = "Email Failed!";
        echo json_encode($hold);
    }
}