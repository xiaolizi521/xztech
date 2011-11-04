<?php 
require_once("CORE_app.php");
require_once("menus.php");
	$computer=new RackComputer;
	$computer->Init("",$computer_number,$db);
    $computer->LogPasswordView();
	$customer_number=$computer->customer_number();
	if( isset($command) and 
        $command == "EDIT_COMPUTER_INFO" and 
        !empty($info) and 
        $info != "Array" )
	{
        foreach( $info as $key=>$value ) {
	        $info[$key] = stripslashes($value);
        }
		$error_message = $computer->EditLoginInfo($info);
		LogPasswordView($computer_number,"EditPassword");
		if ($error_message == "") {
            if( !empty($close_on_finish) ) {
                ?>
<SCRIPT type="text/javascript">
    try {
        opener.top.location.href=opener.top.location.href;
    } catch(e) {
        try {
            opener.location.href=opener.location.href;
        } catch(e) {
            // Can't do anything. :-(
        }
    }
window.close();
</SCRIPT>
<?php
                exit(0);

            } else {
                ForceReload("DAT_display_computer.php3?customer_number=$customer_number&computer_number=$computer_number");
            }
		}

	}
	LogPasswordView($computer_number,"ViewEditPassword");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: Edit Password Info
    </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <script language="JavaScript1.2" src="/script/popup.js" type="text/javascript"></script>
    <?= menu_headers() ?>
</HEAD>
<? require_once("tools_body.php"); ?>
<!-- Begin Edit Password --------------------------------------------------- -->
<?php
if( !in_dept("SUPPORT|NETWORK|PRODUCTION|PROFESSIONAL_SERVICES") ) {
    print '<p style="border: solid red thin; text-align: center; padding: 1ex; width: auto">';
    print "You do not have permissions to view or edit passwords</p>\n";
    exit();
}
?>

	<TABLE BORDER="1"
	       CELLSPACING="0"
	       CELLPADDING="0"
	       ALIGN="left">
	<TR>
		<TD><FORM ACTION="edit_account_info.php" METHOD="POST">
    <?php if( !empty( $close_on_finish ) ) { ?>
        <INPUT TYPE="HIDDEN" NAME="close_on_finish" VALUE="1">
    <?php } ?>
			<INPUT TYPE=HIDDEN NAME=command value="EDIT_COMPUTER_INFO">
			<INPUT TYPE=HIDDEN NAME=customer_number value="<?print($customer_number);?>">
			<INPUT TYPE=HIDDEN NAME=computer_number value="<?print($computer_number);?>">

			<TABLE CELLSPACING=2 CELLPADDING=2 VALIGN="TOP" BORDER=0>
			<TR>
				<TD BGCOLOR="#003399"
			        CLASS="hd3rev"
					COLSPAN=2>Edit Password Info:
					#<?print($customer_number);?>-<?print($computer_number);?></TD>
			</TR>
	        <TR>
			
			<?if (isset($error_message)):?>
			<TR>
				<TD><H1><FONT COLOR="#FF0000"><?print($error_message);?></FONT></H1></TD>
			</TR>
			<?endif;?>
			<?
				$type=$computer->OS();
				//Now handle not showing this info if it is a RAQ customer
				if ($type=="RAQ/RAQ2"||$type=="RAQ3")
					$RAQ=true;
				else
					$RAQ=false;
			?>
				<?if (!$RAQ):?>
					<?
					// Pretty much assume that Win2k should act the same as these guys.
					// Noticed that Chris never added DELL stuff. Not really sure about
					// that, but I assume there was a reason for it and not include our
					// DELL stuff either.
					
                    // This seems to be a problem; you should be able to edit a windows
                    // box's primary info
					// if (!in_array($type, array('NT', 'WIN2K', 'WIN2K Adv Srv', 'WIN2K3', 'Xeon/WIN2K3'))):
					?>
					<TR>
						<TH ALIGN=LEFT> Primary Userid:</TH>
						<TD><INPUT TYPE=TEXT SIZE=20 MAXlength=8 NAME="info[primary_userid]" VALUE="<?HTprint($computer->getData("primary_userid"));?>"></TD>
					</TR>
                    <? if(!$computer->uses_non_durable_passwords()) { ?>
                        <TR>
                            <TH ALIGN=LEFT> Primary Userid Password: </TH>
                            <TD><INPUT TYPE=TEXT SIZE=20 NAME="info[primary_userid_password]" VALUE="<?HTprint($computer->getData("primary_userid_password"));?>"></TD>
                        </TR>
						<TR>
							<TH ALIGN=LEFT> Webmin Password: <i>Should be the same as the one above</i></TH>
							<TD><INPUT TYPE=TEXT SIZE=20 NAME="info[webmin_password]" VALUE="<?HTprint($computer->getData("webmin_password"));?>"></TD>
						</TR>
                    <? } ?>
						<TR>
							<TH ALIGN=LEFT> Webmin Port: <i>60000>x>10000</i></TH>
							<TD><INPUT TYPE=TEXT SIZE=20 maxlength=5 NAME="info[webmin_port]" VALUE="<?HTprint($computer->getData("webmin_port"));?>"></TD>
						</TR>
					<TR>
						<TH ALIGN=LEFT> Rack Password: </TH>
						<TD><INPUT TYPE=TEXT SIZE=20 NAME="info[rack_password]" VALUE="<?HTprint($computer->getData("rack_password"));?>"></TD>
					</TR>
				<?else:?>
					<TR>
						<TH><INPUT TYPE=HIDDEN NAME="info[primary_userid]" VALUE="admin"> &nsbp;</TH>
					</TR>
				<?endif;?>
                    <? if(!$computer->uses_non_durable_passwords()) { ?>
                        <TR>
                            <TH ALIGN=LEFT> Root/Admin Password: </TH>
                            <TD><INPUT TYPE=TEXT SIZE=20 NAME="info[root_password]" VALUE="<?HTprint($computer->getData("root_password"));?>"></TD>
                        </TR>
                    <? } ?>
                    <TR>
                        <TH ALIGN=LEFT> CommVault User Password: </TH>
                        <TD><INPUT TYPE=TEXT SIZE=20 NAME="info[cvuser_password]" VALUE="<?HTprint($computer->getData("cvuser_password"));?>"></TD>
                    </TR>
                    <TR>
                        <TH ALIGN=LEFT>Backup Encryption Password: </TH>
                        <TD><INPUT TYPE=TEXT SIZE=20 NAME="info[mb_user_password]" VALUE="<?HTprint($computer->getData('mb_user_password'));?>"></TD>
                    </TR>
                    <TR>
                        <TH ALIGN=LEFT>Pass Phrase: </TH>
                        <TD><INPUT TYPE=TEXT SIZE=20 NAME="info[ssh_key_passphrase]" VALUE="<?HTprint($computer->getData('ssh_key_passphrase'));?>"></TD>
                    </TR>
                    <TR>
                        <TH ALIGN=LEFT>
                            Public Key:
                        </TH>
                        <TD><TEXTAREA ROWS=4 COLS=40 WRAP=off NAME="info[ssh_key_public]"><?=$computer->getData("ssh_key_public")?></TEXTAREA>
                        </TD>
                    </TR>
                    <TR>
                        <TH ALIGN=LEFT>
                            Private Key:
                        </TH>
                        <TD><TEXTAREA ROWS=4 COLS=40 WRAP=off NAME="info[ssh_key_private]"><?=$computer->getData("ssh_key_private")?></TEXTAREA>
                        </TD>
                    </TR>
					<TR>
						<TH ALIGN=LEFT>
                            Notes:
                            <BR>
                            Use this field to store extraneous secret
                            information
                        </TH>
						<TD><TEXTAREA ROWS=4 COLS=40 WRAP=VIRTUAL NAME="info[notes]"><?=$computer->getData("notes")?></TEXTAREA>
                        </TD>
					</TR>
					<TR>
                        <td align="left">
                            <a href="javascript:makePopUpNamedWin('/py/computer/popupCustomPort.pt?computer_number=<?= $computer_number ?>',200,400,'',3,'CustomPortEditor')" class="text_button">Edit Ports</a>
                        </td>
						<TD ALIGN=RIGHT>
							<INPUT TYPE="image"
						           SRC="/images/button_command_save_off.jpg"
						           BORDER="0"></FORM></TD>
					</TR>
				</TABLE></TD>
			</TR>
	</TABLE>
<!-- End Edit Password ----------------------------------------------------- -->
<?= page_stop() ?>
</HTML>
