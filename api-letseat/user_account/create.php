<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");


    // Should be used to create an account, password, and auto signin

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        

        if(isset($input['email']) &&
           isset($input['name']) 
        ){
            $email = trim($input['email']);
            $name = trim($input['name']);
            $validation = validateEmail($conn,$email);
            if($validation)
            {
                $user_id = makeuser_id($conn,$email);
                $result = create($conn,$user_id,$email,0,$name);
                if($result && isset($input['pass']))
                {
                    $pass = $input['pass'];
                    if(createPassword($conn,$user_id,$pass))
                    {
                        http_response_code(201);
                        $hold = new stdClass();
                        $hold->message = "Success";
                        $hold->result = true;
                        echo json_encode($hold);
                    }else{
                        http_response_code(400);
                        $hold = new stdClass();
                        $hold->message = "Success failed to create passsword";
                        $hold->result = false;
                        echo json_encode($hold);
                    }
                }else{
                    http_response_code(422);
                    $hold = new stdClass();
                    $hold->message = "Failed no password";
                    $hold->result = false;
                    echo json_encode($hold);
                }
            }else{
                http_response_code(409);
                $hold = new stdClass();
                $hold->message = "Failed email already registered";
                $hold->email = $email;
                $hold->validation = $validation;
                $hold->result = false;
                echo json_encode($hold);
            }
        }else{
            http_response_code(422);
            $hold = new stdClass();
            $hold->message = "Failed 422";
            $hold->emailfilter = filter_var($email, FILTER_VALIDATE_EMAIL);
            $hold->email = $email;
            $hold->result = false;
            echo json_encode($hold);
        }
    }elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
            http_response_code(200);
            $hold = new stdClass();
            $hold->message = "Success";
            $hold->result = false;
            echo json_encode($hold);
    }else{
            http_response_code(405);
            $hold = new stdClass();
            $hold->message = "Failed";
            $hold->result = false;
            echo json_encode($hold);
    }
    
    



    function create($conn,$user_id,$email,$authority,$nameIn)
    {   //                          unique  unique int
        $query = 'insert into user_account(user_id,email,name,authority,create_date,modified_date) values(?,?,?,?,?,?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ississ',$user_idIn,$emailIn,$name,$authorityIn,$tstamp,$tstamp);
        
        $user_idIn = $user_id;
        $emailIn = $email;
        $name = $nameIn;
        $authorityIn = $authority;
        $tstamp = date('Y-m-d H:i:s T');
        
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    


function validateEmail($conn,$email)
{
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return userIsAvailible($conn,$email);
    } else {
        // echo("$email is not a valid email address");
        return false;
    }
}


/**
 *  true if no user has the email
 *  false if user is found in db
 */
function userIsAvailible($conn,$emailIn)
{
    $query = "select email from user_account where email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$email);
    $email = $emailIn;

    $result = $stmt->execute();

    $out = '';
    $stmt->bind_result($queryResult);
    $stmt->fetch();
    $out = $queryResult;
    $stmt->close();

    if($result && empty($out))
    {
        return true;
    }else{
        return false;
    }
}


/**
 * creates a user_id based on email hash
 *  handling collisions as well.
 */
function makeuser_id($conn,$email)
{
    $hashedEmail = hash("sha512","$email");
    $outInt = 0;
    for($i=0;$i < 15;++$i)
    {
        $outInt += ord($hashedEmail[$i]);
    }

    $idArr = getuser_ids($conn);
    if(is_array($idArr))
    {
        while(in_array($outInt,$idArr))
        {
            $outInt = ceil($outInt * 2.3);
        }
    }
    return $outInt;
}




function getuser_ids($conn)
{
    $query = "select user_id from user_account";
    $stmt = $conn->prepare($query);

    $result = $stmt->execute();
    $stmt->bind_result($queryResult);

    $row = array();
    while ($stmt->fetch()) {
        $row[] = $queryResult;
    }
    $stmt->close();
    if(empty($row)){
        return false;
    }else{
        return $row;
    }
}


/**
 * adds password to database
 */
function createPassword($conn,$user_id,$inPass)
{
    $query = 'insert into user_pass(user_id,password,create_date,expire_date,modified_date) values(?,?,?,?,?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('issss',$id,$pass,$tstamp,$expire_date,$tstamp);
    $id = $user_id;
    $pass = password_hash($inPass,PASSWORD_DEFAULT);
    $tstamp = date('Y-m-d H:i:s T');
    $expire_date = date('Y-m-d H:i:s T',strtotime($tstamp. '+1 years'));
    
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
