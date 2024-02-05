import json
import glob
import re
from traceback import format_exc 
import sys


rootfolder = sys.argv[1]
project = rootfolder[:-4]
files = glob.glob("./api-" + project + "/*/*.php")

'''
ToDo:
    Make the makequery pull all possible inputs in the correct form with example numbers and strings
    modularize everything
    make changing/setting project data(url, postman name, etc.)
    set up basic testing with the real postman checks
'''



class Postman:
    def __init__(self,info,item,auth,event,variable):
        self.info = info
        self.item = item
        self.auth = auth 
        self.event = event 
        self.variable = variable
    def show(self):
        print(self.info)
        print(self.item)
        print(self.auth)
        print(self.event)
        print(self.variable)
    
        

class info:
    def __init__(self,_postman_id,name,schema):
        self._postman_id = _postman_id
        self.name = name
        self.schema = schema 


class folderitem: # item
    def __init__(self,name,item ):
        self.name = name
        self.item = item 


class item:
    def __init__(self,name,method,request,response ):
        self.name = name 
        self.method = method
        self.request = request
        self.response = response

class request:
    def __init__(self,method, header, url, body):
        self.method = method
        self.header = header
        self.url = url 
        self.body = body


class url:
    def __init__(self, raw,host,path,query):
        self.raw = raw 
        self.host = host 
        self.path = path
        self.query = query

class query:
    def __init__(self,key,value):
        self.key = key
        self.value = value

class auth:
    def __init__(self,ttype,apikey):
        self.ttype = ttype 
        self.apikey = apikey

class apikey:
    def __init__(self,key,value,ttype):
        self.key = key
        self.value = value
        self.ttype = ttype


class event:
    def __init__(self,listen,script):
        self.listen = listen
        self.script = script


class script:
    def __init__(self,ttype,exec):
        self.ttype = ttype
        self.exec = exec
        

class variable:
    def __init__(self,key,value,ttype):
        self.key = key 
        self.value = value
        self.ttype = ttype

def getItemArray(items):
    out = []
    for item in items:
        out.append(makeitem(item))

def makeitem(itemin):
    return item(itemin.name,itemin.method,itemin.request,itemin.response)

def makerequest(requestin):
    return request(requestin.method,requestin.header,requestin.url,requestin.body)


'''
isset($input['
isset($_GET['
'''
def makequeries(urlin): #,queries):
    queryout = ""
    out = []
    if "read" in urlin:
        f = open(urlin, "r") 
        readtext = f.read()
        getkeys = list(set(re.findall(r'\$_GET\[\'(.*?)\'\]', readtext)))
        for queryx in getkeys:
            out.append(query(queryx,"").__dict__)
        queryout = out
    return queryout


def makebody(urlin): #,queries):
    queryout = ""
    out = {}
    if "read" not in urlin:
        f = open(urlin, "r") 
        readtext = f.read()
        keys = list(set(re.findall(r'\$input\[\'(.*?)\'\]', readtext))) 
        for queryx in keys:
            out[queryx] = ""
        queryout = {"mode":"raw","raw":json.dumps(out).replace(', \"',',\r\n    \"').replace('{\"','{\r\n    \"').replace('\"}','\"\r\n}')}
    return queryout

def makeurl(urlin):
    host="{{url}}"
    path=urlin.split("\\")[-2:]
    queryout = makequeries(urlin) #,[{"key":"key","value":"value"},{"key":"key1","value":"value1"}])
    urlin = urlin.replace(urlin.split("\\")[0:1][0],host)
    return url(urlin,host,path,queryout)

def sortme(arr):
    if len(arr) > 0:
        out = [arr[0],arr[2],arr[3],arr[1]]
        return out
    else:
        return []




# body = {"mode":"raw","raw":"{\r\n    \"id\":1,\r\n    \"age_range\":\"0-20\"\r\n}"}
hold = []
old = files[0].split("\\")[1:2][0]

out = []
for fil in files:
    body = makebody(fil)
    if old not in fil:
        hold = sortme(hold)
        out.append(folderitem(old,hold).__dict__)
        hold = []
    if "create" in fil:
        theurl = makeurl(fil)
        header = []
        responsein = []
        requestin = request('POST',header,theurl.__dict__,body).__dict__
        itemin = item("create " + fil.split("\\")[1:2][0],"create",requestin,responsein).__dict__
        hold.append(itemin)
    if "read" in fil:
        theurl = makeurl(fil)
        header = []
        requestin = request('GET',header,theurl.__dict__,body).__dict__
        itemin = item("read " + fil.split("\\")[1:2][0],"read",requestin,responsein).__dict__
        hold.append(itemin)
    if "update" in fil:
        theurl = makeurl(fil)
        header = []
        requestin = request('PUT',header,theurl.__dict__,body).__dict__
        itemin = item("update " + fil.split("\\")[1:2][0],"put",requestin,responsein).__dict__
        hold.append(itemin)
    if "delete" in fil:
        theurl = makeurl(fil)
        header = []
        requestin = request('DELETE',header,theurl.__dict__,body).__dict__
        itemin = item("delete " + fil.split("\\")[1:2][0],"delete",requestin,responsein).__dict__
        hold.append(itemin)
    # print(makeurl("{{url}}/ages/update.php").__dict__)
    old = fil.split("\\")[1:2][0]

hold = sortme(hold)
out.append(folderitem(old,hold).__dict__)
# this can be static
i = {
		"_postman_id": "7e6678b6-fdf6-4539-a457-eaef4e989c3a",
		"name": "api-" + project,
		"schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
	}

# this can be static for a while
a =  {
		"type": "apikey",
		"apikey": [
			{
				"key": "key",
				"value": "token",
				"type": "string"
			}
		]
	}

# this can be static
e = [
		{
			"listen": "prerequest",
			"script": {
				"type": "text/javascript",
				"exec": [
					""
				]
			}
		},
		{
			"listen": "test",
			"script": {
				"type": "text/javascript",
				"exec": [
					""
				]
			}
		}
	]

# This can be static for now
v = [
		{
			"key": "url", 
			"value": "http://localhost/Automation/local/" + rootfolder + "/api-" + project + "/",
			"type": "string"
		}
	]

p = Postman(i,out,a,e,v)
# print(json.dumps(p.__dict__))


with open("tester-" + project + ".json", "w") as outfile:
    json.dump(p.__dict__, outfile)



# Minimum execution -> make a json that holds the variables, folders, and crud calls with methods
