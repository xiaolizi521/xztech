<html>
<HEAD>
    <TITLE>
        CORE: Ticket Text Search
    </TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    

    {literal}
    <script language="Javascript">
        function chooseAge() {
    {/literal}
            selected = {$ticket.age};
    {literal}
            var age = document.getElementById("result_age");
            for (var i=0; i < age.length; i++ ) {
                //alert ( "comparing: " + age.options[i].value + " with " + selected );
                if ( age.options[i].value == selected ) {
                    age.options[i].selected = true; 
                    break;
                }
            }
        }
        function chooseResultsPerPage() {
    {/literal}
            selected = {$ticket.results_per_page};
    {literal}
            var rpp = document.getElementById("results_per_page");
            for (var i=0; i < rpp.length; i++ ) {
                //alert ( "comparing: " + rpp.options[i].value + " with " + selected );
                if ( rpp.options[i].value == selected ) {
                    rpp.options[i].selected = true; 
                    break;
                }
            }
        }
    {/literal}
    </script>
    {php}
     print menu_headers();
    {/php}
</HEAD>
{php}
    if (!empty($GLOBALS['rack_test_system'])) {
        $style = 'style="background-color: #FFAAAA"';
    }
    else {
        $style = '';
    }
    print '<body onload="dynamicMenuPositioning();chooseAge();chooseResultsPerPage();" onresize="dynamicMenuPositioning();">'."\n";
    print '<div ' . $style . ' class="menu_menubar" id="menubar">Loading Menus...</div>'."\n";
{/php}
<div class="menu_page_content" id="maincontent">

