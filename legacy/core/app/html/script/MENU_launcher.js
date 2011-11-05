
//GLOBAL VAR PRESETS
var newURL;
var has_nav = 0;
var mouseX = 0;
var mouseY = 0;

//MOUSE LOCATION DETERMINATION
var isNav = (navigator.appName.indexOf("Netscape") != -1);
var isOpera = (navigator.userAgent.indexOf("Opera") != -1);

function handlerMD(e) {
    Xmd = (isNav) ? e.screenX: event.screenX;
    Ymd = (isNav) ? e.screenY: event.screenY;

    mouseY = Xmd - 20;
    mouseX = Ymd - 50;
    
    if ( isOpera ) {
        mouseX = Ymd -250;
        mouseY = Xmd -50;
    }

}

if (isNav) {
        document.captureEvents(Event.MOUSEDOWN);
}

//MOUSE HANDLER
document.onmousedown = handlerMD;


//POPUP WINDOW FOR THE MENUS
function popUpMenu(urly,w,h) {
    options =   "toolbar=0,status=0,menubar=0,"
                + "resizable=1,width="+w+",height="+h+","  
                + "top=" + mouseX + ",left=" + mouseY;

    if ( has_nav == 1 ) {
        winder.closeMe();
        has_nav = 0;
    }
    
    winder = window.open(urly,"CORE_Menu",options);
    winder.focus();

}

function opner(urly,options) {
    winder = window.open(urly,"CORE_Menu",options);
    winder.focus();
}

//SEND THE OPENER TO THE URL: urly
function sendPrimary(urly) {
    top.core.workspace.location.href = urly;
}

// CHANGE THE TYPE OF SEARCH BASED UPON THE SELECT BOX search_type
function searchChange() {
    arg = document.search_form.search_number.value;
    href = '';
    switch ( document.search_form.search_type[document.search_form.search_type.selectedIndex].value ) 
    {
    case 'info_search':
        if( arg ) {
            href="/CORE_info_search.php?search_number="+arg;
        } else { 
            href="/CORE_info_search.php";
        }
        break;
    case 'super_search':
        document.search_form.submit();
        break;
    case 'ticket_search':
        href="/py/ticket/search.pt";
        break;
    case 'quick_find':
        href="/tools/quick_find.php3";
        break;
    default:
        if( arg ) {
            document.search_form.submit();
        }
        break;
    }

    if( href ) {
        parent.workspace.location.href=href;
        document.location.href=document.location.href;
    }
}

//SET THE SEARCH VALUE
function setSearch()
{
    //No longer used   
}

//DO NUTHIN'
function nuthin()
{
    return true;
}

//DEBUG FUNCTIONS 
function alertMouse() 
{
    alert( "X: " + mouseX + "Y: " + mouseY );
}

function alertInfo() 
{
    alert( "CMD: "  + document.search_form.command.value + "\n" + 
           "-------------------\n" +
           "CST: "  + document.search_form.customer_number.value +"\n"+
           "CPTR: " + document.search_form.computer_number.value +"\n"+
           "APN: "  + document.search_form.agg_product_number.value);
           
}
// Local Variables:
// mode: java
// End:
