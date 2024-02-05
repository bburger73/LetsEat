import os
import sys
import glob
import shutil

project = sys.argv[1]


# change cwd to the dir of this file
dir_path = os.path.dirname(os.path.realpath(__file__))
os.chdir(dir_path)

basenativelocation = 'C:\\Program Files\\Ampps\\www\\Automation\\boilerplate\\basenative\\*.js'
nativelocation = os.path.join(dir_path,project)

files = glob.glob(basenativelocation)
    
print(files)

# os.mkdir(nativelocation)
# os.chdir(os.path.join(dir_path,project))

# create expo project in cwd
# os.system('cmd /k "npx create-expo-app ' + project + '"')

# move all basenative files to current project expo proejct
for fil in files:
    print(fil)
    filename = os.path.basename(fil)
    nativelocationNew = os.path.join(nativelocation,filename)
    print(nativelocation)
    shutil.copyfile(fil,nativelocationNew)


