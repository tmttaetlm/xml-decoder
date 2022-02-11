'use strict';

window.onload = function() {
    document.addEventListener("click", function (event) {
        clickHandler(event.target);
    });
    document.addEventListener("change", function (event) {
        changeHandler(event.target);
    });
}

function clickHandler(obj) {

}

function changeHandler(obj) {
    if (obj.id == "input-file") {
        document.getElementById("span-file").innerText = document.getElementById("input-file").files[0].name;
        document.getElementById("label-file").classList.add("has-file");
        document.getElementById("i-file").classList.add("fa-check");
        document.getElementById("i-file").classList.remove("fa-upload");
        //ajax('decoder.php', function(data){
            //document.getElementById("result").innerText = 
            decoder(obj.files[0].name);
        //}, obj.files[0]);
    }
    if (obj.id == "paymaster-file") {
        document.getElementById("span-paymaster").innerText = document.getElementById("paymaster-file").files[0].name;
        document.getElementById("label-paymaster").classList.add("has-file");
        document.getElementById("i-paymaster").classList.add("fa-check");
        document.getElementById("i-paymaster").classList.remove("fa-upload");
        document.getElementById("paymaster-send").click();
    }
    if (obj.id == "financier-file") {
        document.getElementById("span-financier").innerText = document.getElementById("financier-file").files[0].name;
        document.getElementById("label-financier").classList.add("has-file");
        document.getElementById("i-financier").classList.add("fa-check");
        document.getElementById("i-financier").classList.remove("fa-upload");
        document.getElementById("financier-send").click();
    }
    if (obj.id == "bluecoins-file") {
        document.getElementById("span-bluecoins").innerText = document.getElementById("bluecoins-file").files[0].name;
        document.getElementById("label-bluecoins").classList.add("has-file");
        document.getElementById("i-bluecoins").classList.add("fa-check");
        document.getElementById("i-bluecoins").classList.remove("fa-upload");
        document.getElementById("bluecoins-send").click();
    }
}

function decoder(file) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://10.14.24.134:84/files/"+file, false);
    xhr.setRequestHeader('Content-Type', 'text/xml')
    xhr.send();
    var smses = xhr.responseXML.childNodes[2];
    for (let i = 0; i < smses.attributes.count.value-1; i++) {
        var sms = smses.children[i];
        if (sms.attributes.address.value == '+77719095559' || sms.attributes.address.value == '+77786854888') {
            var sms_block = document.getElementById('result');
            var newdiv = document.createElement('div');
            newdiv.id = 'sms';
            newdiv.className = sms.attributes.type.value == 1 ? 'incoming' : 'outcoming';
            newdiv.dataset.type = sms.attributes.type.value;
            newdiv.dataset.date = sms.attributes.readable_date.value;
            newdiv.innerText = sms.attributes.body.value;
            sms_block.appendChild(newdiv);
            //sms_block.appendChild('<br>');
            console.log(newdiv);
            //console.log(sms.attributes);
        }
    }
}

function ajax(queryString, callback, params)
{
    var f = callback||function(data){};
    var request = new XMLHttpRequest();
    request.onreadystatechange = function()
    {
            if (request.readyState == 4 && request.status == 200)
            {
                f(request.responseText);
            }
    }
    request.open('POST', queryString);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(params);
}