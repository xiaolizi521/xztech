<?php
require_once("CORE_app.php");
if( empty($menu_args) ) {
    $menu_args = '';
}
if( empty($menu_type) ) {
    $menu_type = "";
}
if( empty($customer_number) and !empty($account_number) ) {
    $customer_number = $account_number;
}
if( empty($account_number) and !empty($customer_number) ) {
    $account_number = $customer_number;
}
?>
<HTML>
<HEAD>
    <TITLE>MENU PAGE</TITLE>
    <SCRIPT LANGUAGE="javascript1.2" 
            SRC="/script/MENU_mouse_loc.js" 
            TYPE="text/javascript"></SCRIPT>
    <SCRIPT LANGUAGE="javascript1.2" 
            SRC="/script/MENU_launcher.js" 
            TYPE="text/javascript"></SCRIPT>
    <SCRIPT LANGUAGE="javascript1.2" 
            SRC="/script/popup.js" 
            TYPE="text/javascript"></SCRIPT>
    <SCRIPT LANGUAGE="javascript1.2" 
            TYPE="text/javascript">
var menu_type = '<?=$menu_type?>';
var menu_args = '<?=$menu_args?>';
function navpopup( url, x, y, feature, target ) {
    makePopUpNamedWin( url, y, x, '', feature, target );
}
    </SCRIPT>
    <LINK   REL="stylesheet"  
            HREF="/css/MENU_style.css" 
            TYPE="text/css">
</HEAD>
<BODY   BGCOLOR="#ffffff" 
        LEFTMARGIN=0
        RIGHTMARGIN=0
        TOPMARGIN=0
        BOTTOMMARGIN=0
        MARGINHEIGHT=0
        MARGINWIDTH=0>

<FORM   NAME="search_form"
        METHOD="GET"
        ACTION="/menu/search_redirect.php"
        TARGET="workspace"> 

<TABLE  WIDTH=100% 
        HEIGHT=100% 
        CELLPADDING=2 
        CELLSPACING=0 
        BORDER=0>
  <TR   BGCOLOR=3266CC>
    <!-- BEGIN NAVIGATION MENU BUTTONS -->
    <TD 
        ALIGN=left>
        &nbsp;
        <A  NAME=b_main 
            HREF="javascript:popUpMenu('menu/MENU_main.php',300,350)">
            <IMG SRC="/images/button_menu_main_on.gif"
                 BORDER=0></A>
        <A NAME=b_rep  
           HREF="javascript:popUpMenu('menu/MENU_departments.php',300,350)">
            <IMG SRC="/images/button_department_off.gif"
                 BORDER=0></A>
        <A NAME=b_dept 
           HREF="javascript:popUpMenu('menu/MENU_reports.php',300,510)">
            <IMG SRC="/images/button_reports_off.gif"
                 BORDER=0></A>
<?
//ACTION MENU INITIALIZATION 
if (!isset($action) ) {
    $action = 0;
} elseif (empty($action) ) {
    $action = 0;
} 

if (!empty($action) )
{
    if ($action == "computer")
    {
?>
         <A NAME="b_dept"
            HREF="javascript:popUpMenu('menu/MENU_action_computer.php?computer_number=<?=$computer_number?>',300,600)">
            <IMG SRC="images/button_action_off.gif"
                 BORDER="0"></A>
<?
    } elseif ($action == "account") {
?>
        <A NAME="b_dept"
           HREF="javascript:popUpMenu('menu/MENU_action_account.php?account_number=<?=$account_number?>',300,320)">
            <IMG SRC="images/button_action_off.gif"
                 BORDER="0"></A>
<?
    } elseif ($action == "ticket") {
?>
        <A NAME="b_dept"
           HREF="javascript:popUpMenu('menu/MENU_action_ticket.php?ref_no=<?=$ref_no?>',300,300)">
            <IMG SRC="images/button_action_off.gif"
                 BORDER="0"></A>
<?
    } elseif ($action == "aggprod") {
?>
        <A NAME="b_dept"
           HREF="javascript:popUpMenu('menu/MENU_action_agg_product.php?agg_product_number=<?=$agg_product_number?>&account_number=<?=$account_number?>&customer_number=<?=$customer_number?>',300,300)">
            <IMG SRC="images/button_action_off.gif"
                 BORDER="0"></A>
<?
    } 
} 
?>
</TD>
<!-- END NAVIGATION MENU BUTTONS -->

<!-- SEARCH SECTION -->
    <TD   ALIGN=right style="color: white">
        <INPUT  TYPE=hidden
                NAME=command
                VALUE=FIND_CUSTOMER>
        <SELECT NAME="search_type"
                ONCHANGE="searchChange()">
            <OPTION VALUE="customer_search"> Account </OPTION>
            <OPTION VALUE="computer_search"> Computer </OPTION>
            <OPTION VALUE="ticket_jump"> Ticket # </OPTION>
            <OPTION VALUE="agg_search"> Agg Prod # </OPTION>
            <OPTION VALUE="session_search"> Session # </OPTION>
            <OPTION VALUE="info_search"> Info Search </OPTION>
            <OPTION VALUE="super_search"> Super Search </OPTION>
            <OPTION VALUE="ticket_search"> Ticket Search </OPTION>
        </SELECT>
        <INPUT TYPE=text
               SIZE=11 
               NAME=search_number
               VALUE=""
               onChange="setSearch()">
      <INPUT TYPE="image"
             NAME="go"
              SRC="/images/button_go_off.gif"
           BORDER="0"
            ALIGN="ABSMIDDLE">
    </TD>
<!-- END SEARCH SECTION -->
  </TR>
</TABLE>

</FORM>

<?php
   
if( empty($action) ) {
    set_title( "Welcome" );
}
?>
</BODY>
</HTML>
