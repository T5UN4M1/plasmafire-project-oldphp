function gauge(id,timeStart,timeEnd,time){
    var idFull = id + "Full";

    getId(id).innerHTML = '<div class="gaugeFull" id="'+idFull+'"></div>';
    var buildingTime = timeEnd-timeStart;
    var ellapsedTime = time-timeStart;
    var percent = (ellapsedTime/buildingTime)*100;
    
    getId(idFull).setAttribute("style","width:"+percent + "%;");
    var TO = 0;
    if(buildingTime<300){
        TO = 0.05;
    } else if(buildingTime<1500){
        TO = 0.5;
    } else {
        TO = 5;
    }
    
    setTimeout(function(){updateGauge(idFull,buildingTime,ellapsedTime,TO)},TO*1000);
}
function updateGauge(idFull,buildingTime,ellapsedTime,timeOut){
    ellapsedTime += timeOut;
    var percent = (ellapsedTime/buildingTime)*100;
    if(percent>100){
        percent = 100;
    }
    getId(idFull).innerHTML = '<span style="position:relative;display:block;bottom:15px;font-size:8px">' + percent.toFixed(2) + "%</span>";
    getId(idFull).setAttribute("style","width:"+percent + "%;");
    setTimeout(function(){updateGauge(idFull,buildingTime,ellapsedTime,timeOut)},timeOut*1000);
}