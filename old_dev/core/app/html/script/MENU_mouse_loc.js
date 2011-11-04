var isNav = (navigator.appName.indexOf("Netscape") != -1);
var isOp = navigator.userAgent.indexOf("Opera");

function handlerMD(e){
    Xmd = (isNav) ? e.screenX: event.screenX;
    Ymd = (isNav) ? e.screenY: event.screenY;
    mouseY = Xmd - 20; 
    mouseX = Ymd - 50; 
    if (isOpera > -1) {
        alert('doodoo');
        mouseX = Xmd - 20;
        mouseY = Ymd - 50;
    }
}
