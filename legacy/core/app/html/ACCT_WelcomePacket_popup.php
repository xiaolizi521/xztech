<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

if (!(in_dept("CORE") || in_dept("SALES") || in_dept("SUPPORT"))) {
    trigger_error("You are not authorized to mark welcome packets as sent out", FATAL );
}

checkDataOrExit( array( "account_id" => "Account ID" ) );
$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);

$wp =& $account->getWelcomePacket();

if (empty($in_form)) {
    // Show the form
    ?>
        <HTML>
        <HEAD>
        <TITLE>Welcome Packet</TITLE>
        <LINK HREF="/css/core_ui.css" REL="stylesheet">
        </HEAD>
        <BODY>
            <FORM METHOD=POST ACTION="ACCT_WelcomePacket_popup.php">
                <DIV ALIGN="center"> 
                    <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
                        <TR> 
                            <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Welcome Packet</FONT></B></TD>
                        </TR>
                        <TR> 
                            <TD BGCOLOR="#CCCCCC">
                                <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                                    <TR><TD>
                                        <INPUT TYPE=HIDDEN NAME="account_id" value="<? print $account_id ?>">
                                        <INPUT TYPE=HIDDEN NAME="in_form" value="1">
                                        This account currently is not marked as having a welcome packet sent.<br>
                                        <br>
                                        <INPUT TYPE=SUBMIT NAME=ok VALUE="Mark Welcome Packet as Sent">
                                        <INPUT TYPE=SUBMIT NAME=cancel VALUE="Cancel">
                                    </TD></TR>
                                </TABLE>
                            </TD>
                        </TR>
                    </TABLE>
                </DIV>
            </FORM>
        </BODY>
        </HTML>
    <?
} else {
    // User choose something
    if (!empty($ok)) {
        $wp->markAsSent();
    }
    ?>
        <HTML>
        <HEAD>
        <!-- Refresh calling view -->
        <SCRIPT LANGUAGE="JavaScript">
        <!--
        function close_it() { window.close(); }
        window.opener.location = window.opener.location;
        //-->
        </SCRIPT>
        </HEAD>
        <BODY onLoad="setTimeout(close_it,1)">
        </BODY>
        </HTML>
    <?
}
?>