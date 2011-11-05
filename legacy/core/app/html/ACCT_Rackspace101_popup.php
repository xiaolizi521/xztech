<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( "account_id" => "Account ID" ) );

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);

$rs =& $account->getRackspace101();
$state_name = $rs->getStateName();
$state = $rs->getState();

if( $state != "incomplete" ) {
        $date = $rs->getDate();

        $date = strftime("%Y-%m-%d",strtotime($date));
        
        $text = $rs->getText();
        
        $contact =& new CONT_Contact;
        $contact->loadId( $rs->getContactID() );
        $name = $contact->getName();
}

$chk_completed = "CHECKED";
$chk_bypassed = "";

if( !empty($old_state) and  $old_state != "completed" ) {
    $chk_completed = "";
    $chk_bypassed = "CHECKED";
}

?>
<HTML>
<HEAD>
<TITLE>Rackspace101</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="ACCT_Rackspace101_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Rackspace101</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
<?php
if( $state == "incomplete" ):
?>
                        <TR> 
                          <TD><B>Choose whether the tutorial was completed:</B></TD>
                        </TR>
                        <tr>
                          <td>
                              <p>
                              The Tutorial was:
                              <br>
                              <input type="radio" name="state"
                                     value="completed" <?=$chk_completed ?>>
                              Completed (ignore the notes below)
                              <br>
                              <input type="radio" name="state"
                                     value="by-passed" <?=$chk_bypassed ?>>
                              By-Passed (please fill in notes below)
                          </td>
                        </tr>
                        <TR>
                          <TD>
                              <p>
                              If a customer does not want to take the tutorial
                              now, then press <b>Cancel</b>
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <textarea cols='30' rows='3' name='text'></textarea>
                              <input type='hidden' name='account_id'
                              value='<?=$account_id ?>'>
                     
                          </TD>
                        </tr>
                        <TR> 
                            <TD ALIGN="RIGHT">
                                <INPUT TYPE="submit" NAME="close" VALUE=" Cancel " CLASS="data">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Mark Done " CLASS="data">
                            </TD>
                        </TR>
<?php else: ?>
                        <TR> 
                          <TD><B>Rackspace101 status</B></TD>
                        </TR>
                        <TR>
                          <TD>
                              <p>
                              The Rackspace101 tutorial was
                              <b><?=$state_name ?></b>
                              on
                              <b><?=$date ?></b> by
                              <b><?=$name ?></b>.

                              <?php
                              if( !empty($text) ) {
                                echo "<p>Note: ";
                                echo "<font color='#008020'>";
                                echo "<pre>$text</pre>";
                                echo "</font>\n";
                              }
                              ?>
                          </td>
                        </TR>
                        <TR>
                            <TD ALIGN="RIGHT">
                                <INPUT TYPE="submit" NAME="close" VALUE=" Close " CLASS="data">
                            </TD>
                        </TR>
<?php endif; ?>                        
                    </TABLE>
                </TD>
            </TR>
        </TABLE>
    </DIV>
</FORM>
</BODY>
</HTML>
