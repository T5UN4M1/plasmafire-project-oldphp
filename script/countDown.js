function countDown(id,s) {
    var timeend = Math.floor(Date.now() / 1000) + s;
    countDownUpdate(getId(id),timeend);
}
function countDownUpdate(id,timeend){
    var s = timeend - Math.floor(Date.now() / 1000);
    if(s >= 0){
        id.innerHTML =  sToStr(s);
        setTimeout(function(){countDownUpdate(id,timeend)}, 1000);                                   
    } else {
        F5();
    }
}
function countDownUpdateNoRefresh(id,timeend){
    var s = timeend - Math.floor(Date.now() / 1000);
    if(s >= 0){
        id.innerHTML =  sToStr(s);
        setTimeout(function(){countDownUpdateNoRefresh(id,timeend)}, 1000);                                   
    }
}