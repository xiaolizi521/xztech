<?php require_once("CORE_app.php"); ?>
<HTML id="mainbody">
<HEAD>
	<TITLE>CORE: Confirm: Remove Computer</TITLE>
	<LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0"
       VALIGN="TOP">
<TR>
	<TD>
		<TABLE>
        <?
            $okayToDelete = true;
                $computerBehindResult = $db->SubmitQuery('
                    SELECT "computer_number"
                    FROM "computer_behind_network_device"                        
                    WHERE "device_number" = ' . $computer_number);
                $deviceInFrontResult = $db->SubmitQuery('
                    SELECT "device_number"
                    FROM "computer_behind_network_device"                        
                    WHERE "computer_number" = ' . $computer_number);

                if ($computerBehindResult->numRows() > 0) {
                    $okayToDelete = false;
                ?>
        <TR>
            <TD CLASS="hd3rev"
                BGCOLOR="#003399"> 
                            Cannot Remove Computer
                            #<?="$customer_number-$computer_number"?>
                            because it is in front of computing device<? if($computerBehindResult->numRows() > 1){ print "s"; }?>
                        </TD>
        </TR>
                <? 
                    for ($i = 0; $i < $computerBehindResult->numRows(); $i++) {
                        $deviceNumber = $computerBehindResult->getResult($i, 0);
                        ?>
                        <TR><TD>
                         <A TARGET="_top"
                            HREF="../ACCT_main_workspace_page.php?computer_number=<?=$deviceNumber?>">
                         <?=$deviceNumber?>
                         </A>
                        </TD></TR>
                <?
                    }
                }
                elseif ($deviceInFrontResult->numRows() > 0) {
                    $okayToDelete = false;
                ?>
        <TR>
            <TD CLASS="hd3rev"
                BGCOLOR="#003399"> 
                            Cannot Remove Computer
                            #<?="$customer_number-$computer_number"?>
                            because it is behind computing device<? if($deviceInFrontResult->numRows() > 1){ print "s"; }?>
                        </TD>
        </TR>
                <? 
                    for ($i = 0; $i < $deviceInFrontResult->numRows(); $i++) {
                        $deviceNumber = $deviceInFrontResult->getResult($i, 0);
                        ?>
                        <TR><TD>
                         <A TARGET="_top"
                            HREF="../ACCT_main_workspace_page.php?computer_number=<?=$deviceNumber?>">
                         <?=$deviceNumber?>
                         </A>
                        </TD></TR>
                <?
                    }
                }
        
                $result = $db->SubmitQuery('
                    SELECT "ReferenceNumber"
                    FROM "xref_server_Ticket"
                        JOIN "TCKT_Ticket" USING ("TCKT_TicketID")
                    WHERE computer_number = ' . $computer_number);
                $incontract = $db->SubmitQuery('
                    SELECT "CNTR_ContractID"
                    FROM "CNTR_xref_Contract_Server"
                    WHERE computer_number = ' . $computer_number);

                if ($result->numRows() > 0) {
                    $okayToDelete = false;
                ?>
		<TR>
			<TD CLASS="hd3rev"
			    BGCOLOR="#003399"> 
                            Cannot Remove Computer
                            #<?="$customer_number-$computer_number"?>
                            which has Tickets
                        </TD>
		</TR>
                <? 
                    for ($i = 0; $i < $result->numRows(); $i++) {
                        $refno = $result->getResult($i, 0);
                        ?>
                        <TR><TD>
                         <A TARGET="_top"
                            HREF="/py/ticket/view.pt?ref_no=<?=$refno?>">
                         <?=$refno?>
                         </A>
                        </TD></TR>
                <?
                    }
                }
                elseif ($incontract->numRows() > 0) {
                    $okayToDelete = false;
                ?>
		<TR>
			<TD CLASS="hd3rev"
			    BGCOLOR="#003399"> 
                            Cannot Remove Computer
                            #<?="$customer_number-$computer_number"?>
                            which is part of the following contract(s):
                        </TD>
		</TR>
                <? 
                    for ($i = 0; $i < $incontract->numRows(); $i++) {
                        $refno = $incontract->getResult($i, 0);
                        ?>
                        <TR><TD>
                         <?print("Contract #$refno")?>
                        </TD></TR>
                <?
                    }
                ?>
                    <TR><TD> You must remove the server from the contract before 
                             attempting to remove it from the account.
                    </td></tr>
                <? }
                if($okayToDelete) {
                ?>
		<TR>
			<TD CLASS="hd3rev"
			    BGCOLOR="#003399"> Confirmation: Remove Computer 
                            #<?="$customer_number-$computer_number"?>
                            </TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT> Are you sure you want to permanently 
                        remove computer 
                        #<?="$customer_number-$computer_number"?>
			<A HREF="<?
                            print("/tools/display_computer.php3?"
                                . "customer_number=$customer_number"
                                . "&computer_number=$computer_number"
                                . "&command=DELETE_COMPUTER");
                            ?>">
			<IMG SRC="../images/button_command_yes_off.jpg" WIDTH="100" HEIGHT="24" BORDER="0" ALT="->" ALIGN="ABSMIDDLE"></a></TD>
		</TR>
                <? 
                } 
                ?>
		</TABLE>
	</TD>
</TR>
</TABLE>
<?= page_stop() ?>
</HTML>
