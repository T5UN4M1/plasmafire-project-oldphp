/*
ellapsedTime -> temps qui est déjà passé pour la construction actuelle
amount -> qté restante a construire
amountBuilt -> qté qu'on construit en ...
forXsec -> un nombre donné de secondes
idAmount -> id du span qui contient le nombre de vsx restants
idOverallTimeLeft -> id temps total restant
idTimeLeft -> id temps restant pour construction actuelle
*/
function initShipyard(overallTimeLeft,amount,amountBuilt,forXsec,idAmount,idOverallTimeLeft,idTimeLeft){
    if(forXsec == 1){ // mode X ships / seconde -> pas de gauge & pas de timeleft , que du overall timeleft "fast mode" , ellapsedTime = irrevelant
        var timeend = getTimeStamp() + Math.ceil(amount/amountBuilt);
        updateShipyardFastMode(timeend,amount%amountBuilt,amountBuilt,getId(idAmount),getId(idOverallTimeLeft));
    } else { // mode 1 ships / X secondes -> faut tout update
        var timeend = getTimeStamp() + overallTimeLeft;
        updateShipyardSlowMode(timeend,forXsec,getId(idAmount),getId(idOverallTimeLeft),getId(idTimeLeft));
    }
}
function updateShipyardFastMode(timeend,amount,amountBuilt,idAmount,idOverallTimeLeft){
    var remainingTime = timeend - getTimeStamp();
    if(remainingTime > 0){
        var displayAmount = amount + remainingTime*amountBuilt;
        idAmount.innerHTML = displayAmount;
        idOverallTimeLeft.innerHTML = sToStr(remainingTime);
        setTimeout(function(){updateShipFastMode(timeend,amount,amountbuilt,idAmound,idOverallTimeLeft);},1000);
    } else {
        F5();
    }
    
}
function updateShipyardSlowMode(timeend,forXsec,idAmount,idOverallTimeLeft,idTimeLeft){
    var remainingTime = timeend - getTimeStamp();
    if(remainingTime > 0){
        var displayAmount = Math.ceil(remainingTime/forXsec);
        var ellapsedTime = forXsec - (remainingTime%forXsec);
        idAmount.innerHTML = displayAmount;
        idOverallTimeLeft.innerHTML = sToStr(remainingTime);
        idTimeLeft.innerHTML = sToStr(forXsec - ellapsedTime);
		setTimeout(function(){updateShipyardSlowMode(timeend,forXsec,idAmount,idOverallTimeLeft,idTimeLeft);},1000);
    } else {
        F5();
    }
}