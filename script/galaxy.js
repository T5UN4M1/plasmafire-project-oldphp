function getGalaxyData(){
    g = getId("galaxy").value;
    s = getId("system").value;
    $.post("./gamepages/azax/galaxydata.php",{"g":g,"s":s}).done(function(data) {
        parseGalaxyData(data);
    });
}
function galaxyCheckValues(){
    if(getId("galaxy").value < 1 || isNaN(getId("galaxy").value)){
        getId("galaxy").value = 1;
    } else if(getId("galaxy").value > revData.maxg){
        getId("galaxy").value = revData.maxg;
    }
    if(getId("system").value < 1 || isNaN(getId("system").value)){
        getId("system").value = 1;
    } else if(getId("system").value > revData.maxs){
        getId("system").value = revData.maxs;
    }
}
function change(id,value){
    var actualValue = parseInt(getId(id).value);
    getId(id).value = actualValue +  parseInt(value);
    galaxyCheckValues();
    getGalaxyData();
}
function parseGalaxyData(jsonData){
    var page = '';
    var data = JSON.parse(jsonData);
    page += '<tr><th colspan="3">planète</th><th>Débris</th><th>Propriétaire</th><th>Alliance</th><th>Actions</th></tr>';
    for(var i=1;i<=revData['maxp'];++i){
        page += "<tr>";
        page += "<td>"+i+"</td>";
        
        if(data[i].img != null){
            page += '<td><img style="width:75px;height:75px;" src="'+revData.skin+'planet/'+getPlanetImg(data[i].img)+'" /></td>';
        }else{
            page += "<td></td>";
        }
        
        if(data[i].name != null){
            page += "<td>"+htmlEntities(data[i].name)+"</td>";
        }else{
            page += "<td></td>";
        }
        if(data[i].res0 != null || data[i].res1 != null || data[i].res2 != null){
            page += '<td><img style="width:75px;height:75px;" data-toggle="tooltip" data-placement="bottom" title="Acier : '+data[i].res0+'&#013;Silicium : '+data[i].res1+'&#013;Hydrogène : '+data[i].res2+'" src="'+revData.skin+'pic/debris.jpg" /></td>';
        }else{
            page += "<td></td>";
        }
        if(data[i].username != null){
            page += "<td>"+htmlEntities(data[i].username)+"</td>";
        }else{
            page += "<td></td>";
        }
        if(data[i].tag != null){
            page += "<td>"+htmlEntities(data[i].tag)+"</td>";
        }else{
            page += "<td></td>";
        }
        page += '<td>';
        if(data[i].userid != null){
            page += sendMessage(data[i].userid,revData.skin);
            page += '<a href="#" onclick="sendSpy('+i+')"><img src="'+revData.skin+'pic/r.gif" /></a><br />';
        } else {
            page += '<a href="#" onclick="sendVcolo('+i+')">Coloniser</a><br />';
        }
        if(data[i].res0 != null || data[i].res1 != null || data[i].res2 != null){
            page += '<a href="#" onclick="sendEcolo('+i+')">Recycler</a><br />';
        }
        page += '<a href="?a=fleet&g='+g+'&s='+s+'&p='+i+'">Envoyer une flotte</a>';
        page += "</td></tr>";
        
    }
    $("#galaxyContent").html(page);
}
function sendSpy(p){
    $('#galaxyMsg').load("./gamepages/azax/fleetLauncher.php",{"g":g,"s":s,"p":p,"REQUEST":"STALK"});
    $('#galaxyMsg').html("TEST");
}
function sendVcolo(p){
    $('#galaxyMsg').load("./gamepages/azax/fleetLauncher.php",{"g":g,"s":s,"p":p,"REQUEST":"VCOLO"});
}
function sendEcolo(p){
    $('#galaxyMsg').load("./gamepages/azax/fleetLauncher.php",{"g":g,"s":s,"p":p,"REQUEST":"ECOLO"});
}
getGalaxyData();