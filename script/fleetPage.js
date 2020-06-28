
function checkValue(id,max){
    var n = id.value;
    if(n < 0 || isNaN(n)){
        id.value = 0;
    } else if(n > max){
        id.value = max;
    }
}
function getFleetDisplay(){
    var str = '';
    var missions = ["Attaquer","Transporter","Stationner","Coloniser","Exploiter","Espionner"];
    if(fleets.length > 0){
        str += '<table class="content">';
        str += '<tr><th colspan="4">Flottes en vol</th></tr>';
        str += '<tr><th>Mission</th><th>Heure de départ</th><th>Heure d\'arrivée</th><th>Retour</th></tr>';
        
        for(var i=0;i<fleets.length;++i){
            var Mission = (fleets[i].mission > 99) ? missions[fleets[i].mission%100] + " (retour)" : missions[fleets[i].mission];
            str += '<tr><td>'+Mission+'</td><td>'+getFormatedDate(getTimeStamp() - fleets[i].ellapsedtime)+'</td><td>'+getFormatedDate((getTimeStamp() - fleets[i].ellapsedtime) + fleets[i].duration)+'</td>'+((fleets[i].mission <100) ? '<td id="'+fleets[i].id+'"><a href="#" onclick="requestReturn('+fleets[i].id+')">Retour</a>' : "<td>_") + '</td></tr>';
        }
        str += "</table>";
    }
    return str;
}
function requestReturn(id){
    var data = {
        "REQUEST" : "RETURN",
        "id" : id
    }
    $('#'+id).load("./gamepages/azax/fleetLauncher.php",data);
}
function displayFleetMenu1(){
    var page = "";
    page += '<form><table class="content"><tr><th colspan="2">Lancer une flotte</th></tr>'
    for(var i=0;i<ships.length;++i){
        page +=  '<tr><td>' + ships[i].name + '('+format(ships[i].amount)+')</td>';
        page +=  '<td><input type="text" onkeyup="checkValue(this,'+ships[i].amount+')" value="0" id="'+ships[i].id+'" /> <a href="#" onclick="setMax(\''+ships[i].id+'\','+ships[i].amount+')">Max</a></td></tr>';
    }
    page += '<tr><td colspan="2"><input type="button" onclick="displayFleetMenu2()" value="Envoyer" /></td></tr>'
    page += '</table></form>'
    $('#main').html(getFleetDisplay() + page);
}
function displayFleetMenu2(){
    shipsSelected = [];
    fleetMaxSpeed = 9999999999999999999999999;
    var vcolo = false;
    var ecolo = false;
    var yuno = false;
    for(var i=0;i<ships.length;++i){ // on recupere les vsx selectionnés
        if(getId(ships[i].id).value > 0){
            shipsSelected.push(ships[i]);
            shipsSelected[shipsSelected.length-1].amount = getId(ships[i].id).value;
            if(ships[i].move.total < fleetMaxSpeed){
                fleetMaxSpeed = ships[i].move.total;
            }
            switch(ships[i].id){
                case 205: // STALKING
                    yuno = true;
                    break;
                case 213: // COLO
                    vcolo = true;
                    break; // CYCLAGE
                case 212:case 217:
                    ecolo = true;
                    break;
            }
        }
    }
    if(shipsSelected.length == 0){
        displayFleetMenu1();
        return 0;
    }
    
    var page = '';
    var destg = (revData.g != null) ? revData.g : revData.startg;
    var dests = (revData.s != null) ? revData.s : revData.starts;
    var destp = (revData.p != null) ? revData.p : revData.startp;
    page += '<table class="content"><tr><th colspan="2">Paramètres du trajet</th></tr>';
    page += '<tr><td>Départ</td><td>'+planetLocation(revData.startg,revData.starts,revData.startp)+'</td></tr>';
    page += '<tr><td>Destination</td><td><input type="text" id="endg" name="end[g]" value="'+destg+'" size="2" onkeyup="urg()" /> : <input type="text" id="ends" name="end[s]" value="'+dests+'" size="2" onkeyup="urg()" /> : <input type="text" id="endp" name="end[p]" value="'+destp+'" size="2" onkeyup="urg()" /></td></tr>'
    page += '<tr><td>Programmation de la vitesse</td><td><select name="speed" id="speed" onchange="urg()" >';
    for(var i=100;i>0;i-=5){
        page += '<option value="'+i+'">'+i+'%</option>';
    }
    page += '<option value="1">1%</option></select></td></tr>';
    page += '<tr><td>Vitesse</td><td id="fleetSpeed">_</td></tr>';
    page += '<tr><td>Distance</td><td id="distance">_</td></tr>';
    page += '<tr><td>Durée du trajet</td><td id="duration">_</td></tr>';
    page += '<tr><td>Consommation</td><td id="consumption">_</td></tr>';
    page += '<tr><td>Capacité de stockage</td><td id="fret">_</td></tr>';
    page += '<tr><th colspan="2">Paramètres de la mission</th></tr>';
    page += '<tr><td>Mission</td><td><select name="mission" id="mission">';
    var missions = ["Attaquer","Transporter","Stationner","Coloniser","Exploiter","Espionner"];
    var selected = false;
    for(var i=0;i<missions.length;++i){
        if((yuno && i==5)||(ecolo && i==4)||(vcolo && i==3)){
            page += '<option value="'+i+'" selected="selected">' + missions[i] + "</option>";
        } else if(i<3){
            page += '<option value="'+i+'">' + missions[i] + "</option>";
        }
    }
    page += "</select></td></tr>";
    page += '<tr><th colspan="2">Ressources à embarquer</th></tr>';
    for(var i=0;i<revData.ressources.length;++i){
        page += '<tr><td>' + revData.ressources[i].name + '</td><td><input type="text" name="cargo['+i+']" id="cargo'+i+'" value="0" onkeyup="urg()" /><a href="#" onclick="maxRes(\''+i+'\')">Max</a></td></tr>';
    }
    page += '<tr><td>Capacité de stockage restante</td><td id="roomLeft">_</td></tr>';
    page += '<tr><td colspan="2"><input type="button" onclick="sendData()" value="Hajime!" /></td></tr>';
    page += "</table>";
    $('#main').html(page);
    urg();
}



function urg(){
    var endg = getId("endg").value;
    var ends = getId("ends").value;
    var endp = getId("endp").value;
    var speedPercent = getId("speed").value / 100.0;
    var cargo = [];
    for(var i=0;i<revData.ressources.length;++i){
        cargo.push(getId("cargo"+i).value);
    }
    checkMaxLocation();
    getTotalFret();
    getDistance();
    getFleetRealSpeed();
    getConsumption();
    getDuration();
    checkCargo();
}
function checkMaxLocation(){
    if(getId("endg").value < 1 || isNaN(getId("endg").value)){
        getId("endg").value = 1;
    } else if(getId("endg").value > revData.maxg){
        getId("endg").value = revData.maxg;
    }
    if(getId("ends").value < 1 || isNaN(getId("ends").value)){
        getId("ends").value = 1;
    } else if(getId("ends").value > revData.maxs){
        getId("ends").value = revData.maxs;
    }
    if(getId("endp").value < 1 || isNaN(getId("endp").value)){
        getId("endp").value = 1;
    } else if(getId("endp").value > revData.maxp){
        getId("endp").value = revData.maxp;
    }
}
function getTotalFret(){
    fret = 0;
    for(var i=0;i<shipsSelected.length;++i){
        fret += shipsSelected[i].fret * shipsSelected[i].amount;
    }
    $("#fret").html(format(fret));
}
function getDistance(){
    distance = 0;
    if(revData.startg != getId("endg").value){ // autre galaxie
        distance = 4000 + Math.abs(getId("endg").value - revData.startg) * 1000;
    } else if(revData.starts != getId("ends").value){ // autre system
        distance = 750 + Math.abs(getId("ends").value - revData.starts) * 25;
    } else if(revData.startp != getId("endp").value){ // autre position
        distance = 250 + Math.abs(getId("endp").value - revData.startp) * 10;
    } else { // meme planete
        distance = 5;
    }
    $("#distance").html(format(distance));
}
function getFleetRealSpeed(){
    fleetRealSpeed = fleetMaxSpeed * (getId("speed").value / 100);
    $("#fleetSpeed").html(format(fleetRealSpeed));
    return fleetRealSpeed;
}
function getConsumption(){
    consumption = 0;
    for(var i=0;i<shipsSelected.length;++i){
        consumption += (shipsSelected[i].move.consumption * (fleetRealSpeed / shipsSelected[i].move.total) * shipsSelected[i].amount)/10000.0;
    }
    consumption *= distance;
    if(consumption <= revData.ressources[2].amount){
        $("#consumption").removeClass().addClass('good');
    } else {
        $("#consumption").removeClass().addClass('bad');
    }
    $("#consumption").html(format(consumption));
}
function getDuration(){
    duration = Math.ceil((distance / fleetRealSpeed) * (100000.0/revData.fleetspeed));
    
    $("#duration").html(sToStr(duration));
}
function checkCargo(){
    var total = 0;
    for(var i=0;i<revData.ressources.length;++i){
        if(getId("cargo"+i).value < 0 || isNaN(getId("cargo"+i).value)){
            getId("cargo"+i).value = 0;
        } else if(getId("cargo"+i).value > revData.ressources[i].amount){
            getId("cargo"+i).value = revData.ressources[i].amount;
        }
        total += parseInt(getId("cargo"+i).value);
    }
    if(mission == 0){
        for(var i=0;i<revData.ressources.length;++i){
            getId("cargo"+i).value = 0;
        }
    }
    roomLeft = fret - total;
    if(roomLeft > 0){
        $("#roomLeft").removeClass().addClass('good');
    } else if(roomLeft == 0){
        $("#roomLeft").removeClass().addClass('presque');
    } else {
        $("#roomLeft").removeClass().addClass('bad');
    }
    $("#roomLeft").html(format(roomLeft));
    
}
function maxRes(resId){
    getId("cargo"+resId).value = parseInt(getId("cargo"+resId).value) + roomLeft;
    checkCargo();
}
function sendData(){
    // gathering data
    var ressources = [];
    for(var i=0;i<revData.ressources.length;++i){
        ressources.push(parseInt(getId("cargo"+i).value));
    }
    var chips = [];
    for(var i=0;i<shipsSelected.length;++i){
        chips.push({"id":shipsSelected[i].id,"amount":shipsSelected[i].amount});
    }
    var data = {
        "ships" : chips,
        "endg" : parseInt(getId("endg").value),
        "ends" : parseInt(getId("ends").value),
        "endp" : parseInt(getId("endp").value),
        "speed" : parseInt(getId("speed").value),
        "mission" : parseInt(getId("mission").value),
        "ressources" : ressources
    };
    //var jsonData = JSON.stringify(data);
    $('#main').html('<table class="content"><tr><td id="result"></td></tr></table>');
    $('#result').load("./gamepages/azax/fleetLauncher.php",data);
}
displayFleetMenu1();