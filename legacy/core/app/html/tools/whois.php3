<?require_once("CORE_app.php");?>
	<TITLE>CORE: Reverse Whois</TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<FORM ACTION=whois.php3 METHOD="POST">
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev" COLSPAN="2"> WHOIS </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> IP Address: </TD>
			<TD> <INPUT TYPE=TEXT name="ip" value="<?if(!empty($ip)) { print $ip; }?>"> </TD>
		</TR>
		<TR>
			<TD COLSPAN="2"> <INPUT TYPE="image"
			            SRC="/images/button_command_search_off.jpg"
			            BORDER="0"> </TD>
		</TR>
		<?if (!empty($ip)):?>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> Results: </TD>
			<TD>
			<?
				$output=system("host $ip");
				$info=split("pointer",$output);
				//$output=$info[1];
				print("$output");
			?> </TD>
		</TR>
		<?endif;?>
		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<?$db->CloseConnection();?>
</BODY>
</HTML>
