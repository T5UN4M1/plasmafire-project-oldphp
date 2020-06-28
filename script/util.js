function getId(id){
    return document.getElementById(id);
}
function F5(){
    window.location.reload(true);
}
function sToStr(s){
    return  ((s>=86400) ?  Math.floor(s/86400)    + 'j ' : '') +
            ((s>=3600)  ? (Math.floor(s/3600)%24) + 'h ' : '') +
            ((s>=60)    ? (Math.floor(s/60)%60)   + 'm ' : '') + 
                                     (s%60)       + 's';
}
function getTimeStamp(){
    return Math.floor(Date.now() / 1000);
}
function getTimestamp(){
    return getTimeStamp();
}
function setMax(id,value){
    $("#"+id).attr("value",value);
}
function format(n){ // http://stackoverflow.com/questions/9743038/how-do-i-add-a-thousand-seperator-to-a-number-in-javascript 
    n = parseInt(n).toFixed(0);
    var rx=  /(\d+)(\d{3})/;
    return String(n).replace(/^\d+/, function(w){
        while(rx.test(w)){
            w= w.replace(rx, '$1.$2');
        }
        return w;
    });
}
function planetLocation(g,s,p){
    return g + ':' + s + ':' + p;
}

function getFormatedDate(timestamp){ // http://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript
 
    var a = new Date(timestamp * 1000);
    var year = a.getFullYear();
    var month = a.getMonth();
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    var time = date + '/' + month + '/' + year + ' à ' + hour + ':' + min + ':' + sec ;
    return time;
}
function htmlEntities(str) { // https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function getPlanetImg(n){
    if(n >9){
        return "00"+n+".png";
    }else {
        return "000"+n+".png";
    }
}
function tip(){
    $('[title]').qtip({
    style: { classes: 'ttp' },
    position: {effect: false},
    position: {
        target: 'mouse',
        adjust: {
            mouse: true,
            x:-5,
            y:10
        }
    },
    show: {delay: 0}
});
}
function sendMessage(id,skin){
    return '<a href="?a=sendmsg&id='+id+'"><img src="'+skin+'pic/m.gif" /></a>';
}
function confirmRedirect(url,msg){
    if (confirm(msg)){
        window.location.replace(url);
    }
}