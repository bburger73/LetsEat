from email.mime import base
import mysql.connector
import os

# importing required packages
from pathlib import Path
import shutil
import sys

# Get everything from basenative and merge with database below


project = sys.argv[1][:-4]

hostIn="localhost"
userIn="root"
passwordIn="mysql"
databaseIn=project

project = "api-" + project
baseapilocation = 'C:/Program Files/Ampps/www/Automation/boilerplate/baseapi'

mydb = mysql.connector.connect(
    host=hostIn,
    user=userIn,
    password=passwordIn,
    database=databaseIn
)



'''
Creates a quick and dirty api based on tables from a database in the current working directory

TODO:
    add support for multiple databases
    add support for whole server database hosted... idk how
    add support for copying databases to folders.
    add support for unifying database api folders

    x add return values and server codes for success and failures
    x allow doubles in getTypeLetter
    x fix $_GET issue
    x pull copy of the database and place it in root of api folder
    x get update to update each individual column and update only set elements.
    x fix update id issue.
    x on create set up for on no insert fail with some 4** error.
    x on failure 40x read a POST -> failed isset()
    x set up authkey in login for all api calls?
    x detect if auto increment key and assign as zero
    x create return insert_id    
    x export baseline postman key -> resolved by generating $test=1 to fix $userId
    x check for delete id is set
    
    create,delete,update reciepts. tstamp updated and id of item... Should be enforced by db as well(maybe update db to do these dates?)
    read from tstamp last mod

    Encapsulate all methods
    
    create a response with list of inputs.
'''

def dumpDatabase(project,user,database,password):
    import os
    location = os.path.join(".\\", project,database + '.sql')
    print(location)
    oscall = ('mysqldump -u {0} -p{1} {2} > {3}').format(user,password,database,location)
    os.system(oscall)


def getTypeLetter(typein):
    if "int" in typein:
        return "i"
    if "double" in typein:
        return "d"
    else:
        return "s"


def createFile(location,input):
    if not os.path.exists(location):
        f = open(location, "w")
        f.write(input)
        f.close()
    else:
        print("Files Exist: " + location)



def createCreate(query,types,questionmarks,columns,table,autoincrement):
    columnsArray = columns.split(',')
    fromGet = ""
    issetArray = []
    for column in columnsArray:
        # print(column[1:] + ":col ai:" + autoincrement + " val:" + str(column[1:] == autoincrement))
        if column[1:] == autoincrement:
            fromGet = fromGet + """
            """ + column + """ = 0;"""
        else:
            issetArray.append("""isset($input['""" + column[1:] + """'])""")
            fromGet = fromGet + """
            """ + column + " = $input['" + column[1:] + "'];"""
    # print(issetArray)
    issetArrayString = " && ".join(issetArray)
    start = """<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $userId = checkAuth($conn);

        if(""" + issetArrayString + """){
            """ + fromGet + """

            create($conn,""" + columns + """);
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
    
    function create($conn,""" + columns + """)
    {
        $query = '""" + query + """';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('""" + types + """',""" + questionmarks + """);
        
        """
    for input in columns.split(','):
        start = start + "" + input + "In = " + input + ";" + """
        """
    function = start + """
        $result = $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        if($result)
        {
            http_response_code(200);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->id = $id;
            $hold->message = "Success";
            echo json_encode($hold);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            echo json_encode($hold);
        }
    }
    """
    location = os.path.join(parent_dir, project ,table,'create.php')#https://api.involvedk12.org') 
    createFile(location,function)


def createRead(command,id,types,table):
    outputs = '$' + columns.replace(',',',$')
    start = """<?php
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

        if(isset($_GET['""" + id + """'])){
            $id = $_GET['""" + id + """'];//$_GET['id'];
            echo read($conn,$id);
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


    function read($conn,$""" + id + """)
    {
        $query = '""" + command + """';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('""" + types + """',$""" + id + """In);
        
        """
    input = columns.split(',')[0]
    start = start + "$" + input + "In = $" + input + ";" + """
    """
    function = start + """
        $result = $stmt->execute();
        $stmt->bind_result(""" + outputs + """);
        $stmt->fetch();
        
        $hold = new stdClass();
        $hold->result = $result;
        """
    for column in columns.split(','):
        function = function + "$hold->" + column + " = $" + column + """;
        """

    function = function + """
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
        $query = 'select * from """ + table + """';
        $stmt = $conn->prepare($query);        
        """
    function = function + """
        $result = $stmt->execute();
        $out = array();
        $stmt->bind_result(""" + outputs + """);
        while($stmt->fetch())
        {
            $hold = new stdClass();
            """
    for column in columns.split(','):
        function = function + "$hold->" + column + " = $" + column + """;
            """

    function = function + """
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
    """
    location = os.path.join(parent_dir, project ,table,'read.php')#https://api.involvedk12.org') 
    createFile(location,function)


def createUpdate(command,columns,types,table):
    columnsArray = columns.split(',')
    fromPut = ""
    for column in columnsArray:
        fromPut = fromPut + """
        """ + column + " = $input['" + column[1:] + "'];"

    paramList = columns.replace(",","In,") + "In," + columns.split(',')[0] + "In"
    start = """<?php
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
    """
    subTypeColum = 0
    loopColumns = columns.split(',')
    for input in loopColumns[1:]:
        subTypeColum = subTypeColum + 1
        start = start + """        
        if( isset($input['""" + loopColumns[0][1:] + """']) and
            isset($input['""" + input[1:] + """'])){
            """ + loopColumns[0] + """ = $input['""" + loopColumns[0][1:] + """'];
            """ + input + """ = $input['""" + input[1:] + """'];
            $val = update""" + input[1:] + """($conn,""" + loopColumns[0] + """,""" + input + """);
            $out->""" + input[1:] + """ = $val;
            $output = $output && $out;
            $someupdates = true;
        }"""
    start = start + """
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

    function update($conn,""" + columns + """)
    {
        $query = '""" + command + """';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('""" + types + """i',""" + paramList + """);
        
        """
    for input in columns.split(','):
        start = start + "" + input + "In = " + input + ";" + """
        """
    start = start + """
        $result = $stmt->execute();
        $stmt->close();
        
        if($result)
        {
           return true;
        }else{
            return false;
        }
    }
    """
    subTypeColum = 1
    loopColumns = columns.split(',')
    for input in loopColumns[1:]:
        paramList = input + "In," + loopColumns[0] + "In"
        subtypes = types[subTypeColum] + types[0]
        subTypeColum = subTypeColum + 1
        start = start + """
    function update""" + input[1:] + """($conn,""" + loopColumns[0] + """,""" + input + """){
        $query = 'update """ + table + """ set """ + input[1:] + """=? where """ + loopColumns[0][1:] + """=?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('""" + subtypes + """',""" + paramList + """);
        """ + input + "In = " + input + ";" + """
        """ + loopColumns[0] + "In = " + loopColumns[0] + ";" + """
        $result = $stmt->execute();
        $stmt->close();
        if($result)
        {
           return true;
        }else{
            return false;
        }
    }
    """
    function = start
    location = os.path.join(parent_dir, project ,table,'update.php')#https://api.involvedk12.org') 
    createFile(location,function)



def createDelete(command,id,types,table):
    start = """<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    require_once './../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die("Can't Connect");

    if($_SERVER['REQUEST_METHOD'] === 'DELETE')
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $userId = checkAuth($conn);
        if(isset($input['id'])){
            $id = $input['id'];
            delete($conn,$id);
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


    function delete($conn,""" + id + """In)
    {
        $query = '""" + command + """';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('""" + types + """',""" + id + """);
        
        """ + id + """ = """ + id + """In;""" + """
        """
    function = start + """
        $result = $stmt->execute();
        $stmt->close();
                
        if($result)
        {
            http_response_code(200);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Success";
            echo json_encode($hold);
        }else{
            http_response_code(400);
            $hold = new stdClass();
            $hold->result = $result;
            $hold->message = "Failed";
            echo json_encode($hold);
        }
    }
    """
    location = os.path.join(parent_dir, project ,table,'delete.php')#https://api.involvedk12.org') 
    createFile(location,function)


def createLogin(hostname,database,user,password):
    function = """<?php
    $hn = '""" + hostname + """';
    $db = '""" + database + """';
    $un = '""" + user + """';
    $pw = '""" + password + """';
    
    $test = true;
    $auth = "";
    if(isset($_SERVER['HTTP_""" + database.upper() + """_AUTHKEY']))
    {
        $auth = $_SERVER['HTTP_""" + database.upper() + """_AUTHKEY'];
    }

    $url = $_SERVER['HTTP_HOST'];
    date_default_timezone_set('UTC');
    
    function checkAuth($conn)
    {
        global $auth;
        global $test;
        if($test){
            return 1;
        }

        $query = 'select UserId from usertoken where Token=? and ExDate>?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss',$authIn,$todayDate);
        
        $authIn = $auth;
        $todayDate = date("Y-m-d H:i:s T");
        
        $result = $stmt->execute();
        $stmt->bind_result($idOut);
        $stmt->fetch();
        $stmt->close();
        if($result and $idOut > 0)
        {
            return $idOut;
        }else{
            http_response_code(401);
            $hold = new stdClass();
            $hold->success = "failed";
            $hold->result = false;
            echo json_encode($hold);
            exit();
        }
    }


    function depricate(){
        http_response_code(410);
        $hold = new stdClass();
        $hold->message = "Api Removed";
        $hold->result = false;
        $hold->code = 2;
        echo json_encode($hold);
        exit();
    }

    """
    location = os.path.join(parent_dir, project ,'login.php')#,'https://api.involvedk12.org') 
    createFile(location,function)



def executeScriptsFromFile(filename):
    cursor = mydb.cursor()
    fd = open(filename, 'r')
    sqlFile = fd.read()
    fd.close()
    sqlCommands = sqlFile.split(';')

    for command in sqlCommands:
        try:
            if command.strip() != '':
                cursor.execute(command)
        except IOError as msg:
            print("Command skipped: ", msg)



mycursor = mydb.cursor()



mycursor.execute("show tables")

myresult = mycursor.fetchall()
tables = []
parent_dir = os.path.abspath(os.getcwd())
path = os.path.join(parent_dir,project)
originpath = path
if not os.path.exists(path):
    os.mkdir(path)

for x in myresult:
    mycursor.execute("describe `" + x[0] + "`")
    resultTable = mycursor.fetchall()
    columns = []
    types = []

    path = os.path.join(parent_dir, project ,x[0]) 
    if not os.path.exists(path):
        os.mkdir(path)
    autoincrement = ''
    for y in resultTable:
        columns.append(y[0])
        holdtype = getTypeLetter(y[1].decode("utf-8")) 
        # print(y[5])
        if y[5] == 'auto_increment':
            autoincrement = y[0]
        types.append(holdtype)
    table = [x[0],columns,types,autoincrement]
    tables.append(table)
    
#insert into table(columns) values(questionmarks)
for z in tables:
    table = z[0]
    columnsPlain =','.join(str(x) for x in z[1])
    columns = '$' + ',$'.join(str(x) for x in z[1])
    types = ''.join(str(x) for x in z[2])
    id = z[1][0]
    autoincrement = z[3]
    questionmarks = ','.join("?" for x in z[1])
    inputs = ','.join("$" + str(x) + "In" for x in z[1])
    commnd = "insert into " + table + "(" + columnsPlain + ") values(" + questionmarks + ")"
    createCreate(commnd,types,inputs,columns,table,autoincrement)
    #print(commnd)

#select columns from table
for z in tables:
    table = z[0]
    columns = ','.join(str(x) for x in z[1])
    types = z[2][0]
    id = z[1][0]
    commnd = "select " + columns + " from " + table + " where " + id + "=?"
    createRead(commnd,id,types,table)
    #print(commnd)

#update table set columns=questionmark where id=?
for z in tables:
    table = z[0]
    columns = '$' + ',$'.join(str(x) for x in z[1])
    types = ''.join(str(x) for x in z[2])
    id = z[1][0]
    questionmarks = '=?,'.join(str(x) for x in z[1]) + "=?"
    command = "update " + table + " set " + questionmarks + " where " + id + "=?" 
    createUpdate(command,columns,types,table)
    #print(commnd)

#delete from table where id=?
for z in tables:
    table = z[0]
    columns = '$' + ',$'.join(str(x) for x in z[1])
    id = z[1][0]
    commnd = "delete from " + table + " where " + id + "=?" 
    createDelete(commnd,'$' + id,z[2][0],table)
    #print(commnd)

# Should combine databases

executeScriptsFromFile(baseapilocation + "/testcombine.sql")
mydb.commit()

# add user code to boilerplate(may need to mod boilerplate to have non-overlapping calls)
shutil.copytree(baseapilocation, originpath, dirs_exist_ok=True)

os.remove(os.path.join(originpath,"login.php"))
createLogin(hostIn,databaseIn,userIn,passwordIn)
dumpDatabase(project,userIn,databaseIn,passwordIn)

#SELECT id, first_name, last_name, phone, address FROM client WHERE 1
#INSERT INTO client(id, first_name, last_name, phone, address) VALUES (?,?,?,?,?)
#UPDATE client SET id=?,first_name=?,last_name=?,phone=?,address=?
#DELETE FROM client WHERE id=?