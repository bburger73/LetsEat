var host = window.location.protocol + "//" + window.location.host;


function updatePassword()
{
    let url_string = window.location.href;
    let url = new URL(url_string);
    let token = url.searchParams.get("token");
    let newPass = document.getElementById("password").value;


    setPassword(token,newPass);
}

function setPassword(token,newPass)
{
    const params = "token=" + token + "&password=" + newPass;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", host + "/forgot/reset.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            window.location.href = host;
        }
    };
    xmlhttp.send(params);
}

function verPass()
{
    let pass = document.getElementById("password").value;
    let passVer = document.getElementById("passwordVer").value;

    if(pass !== passVer)
    {
        document.getElementById("hint").className = "visible-hint";
    }else{
        document.getElementById("hint").className = "hidden-hint";
    }
}
