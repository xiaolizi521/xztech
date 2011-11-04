<?php
// This is ALL you need!
//require_once("CORE_app.php");
?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Account Summary
    </TITLE>
	<LINK href="/css/core_ui.css" rel="stylesheet">
</HEAD>
<BODY MARGINWIDTH="0" MARGINHEIGHT="0" onload="initGroup('menu')" LEFTMARGIN=0 TOPMARGIN=0 BGCOLOR="#FFFFFF">

<?php
/*
	//Initialize Contact
	$contact = new CONT_Contact;
	$contact->LoadID( $id );
*/
?>
<TABLE>
<TR>
	<TD VALIGN="top">
		<!-- Begin Contact Details ------------------------------------------------  -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN="left">
		<TR>
			<TD><IMG SRC="/images/tbl-left-top.jpg" WIDTH="20" HEIGHT="25" BORDER="0" ALT=""></TD>
			<TD BGCOLOR="#003399" CLASS="HD3REV">
			<?php
			/*
			$person = $contact->getPerson();
			print $person->getFirstName()." ";
			print $person->getLastName() ." ";
			print ", " . $contact->getTitle() . " ";
			//print $contact->getAccountRole();
			print "#" . $person->getID();
			*/
			?>
			</TD>
			<TD><IMG SRC="/images/tbl-right-top.jpg" WIDTH="10" HEIGHT="25" BORDER="0" ALT=""></TD>
		</TR>
		<TR>
			<TD BACKGROUND="assets/images/tbl-left-tile.jpg">&nbsp;</TD>
			<TD>
				<TABLE>	
<?php
/*		
		$address = $contact->getAddress();	    
		print ("<TR><TD>" . $address->getStreet() . "<BR>" . $address->getCity() . ", " . $address->getState() . " " . $address->getPostalCode() . " " . $address->getCountry() . "</TD></TR>");
		
		
		$emails =& $contact->getEmails();
	    $emails->reset();
	    do {
	            $part = $emails->getCurrent();
	            $email =& $part->getEmail();
	            $type  = $part->getContactEmailType();
	            
				print("<TR><TD><A HREF=\"mailto:" . $email->getAddress() . "\">" . $email->getAddress() . "</A> (" . $type->getName() . ")</TD></TR>");

	    } while( $emails->next() );		

	    $phones =& $contact->getPhoneNumbers();
	    $phones->reset();
	    do {
	            $part = $phones->getCurrent();
	            $phone =& $part->getNumber();
				$country =& $phone->getCountry();
				$phone_type =& $phone->getPhoneType();
	            
				print("<TR><TD>(". $country->getAbbrev() .") ");
				print $phone->getNumber();
				print (" (". $phone_type->getName().")</TD></TR>");
				
		} while( $phones->next() );		
*/
?>	
			</TABLE>
			</TD>
		</TR>
		<TR ALIGN="left" VALIGN="top" BGCOLOR="#003399">
			<TD ALIGN="left" VALIGN="bottom"><IMG SRC="/images/tbl-left-bottom.jpg" WIDTH="20" HEIGHT="8" BORDER="0" ALT="" ALIGN="TOP"></TD>
			<TD BGCOLOR="#003399" class="smhd">&nbsp;</TD>
			<TD HEIGHT="8" ALIGN="left" VALIGN="bottom"><IMG SRC="/images/tbl-right-bottom.jpg" WIDTH="10" HEIGHT="8" BORDER="0" ALT="" ALIGN="TOP"></TD>
		</TR>
		</TABLE>
		<!-- End Contact Details --------------------------------------------------  -->
	</TD>
	<TD VALIGN="top">
		<!-- Begin Notes --------------------------------------------------- -->
		<TABLE WIDTH="160" BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD BGCOLOR="#FFCC33" VALIGN="top">
			<IMG SRC="/images/note_corner.gif" WIDTH="10" HEIGHT="10" HSPACE="0" VSPACE="0" BORDER="0" ALIGN="TOP" ALT="">
			&nbsp; belmendo </TD>
			<TD ALIGN="right" BGCOLOR="#FFCC33"> 02/16/2002 4:30pm </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#FFF999" COLSPAN=2><BR>Customer needs more servers for new project. Is doing online order processing for sales reps in the field.<BR></TD>
		</TR>
		</TABLE>
		<BR CLEAR="all">
		<TABLE WIDTH="160" BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD BGCOLOR="#FFCC33" VALIGN="top">
			<IMG SRC="/images/note_corner.gif" WIDTH="10" HEIGHT="10" HSPACE="0" VSPACE="0" BORDER="0" ALIGN="TOP" ALT="">
			&nbsp; belmendo </TD>
			<TD ALIGN="right" BGCOLOR="#FFCC33"> 02/16/2002 4:30pm </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#FFF999" COLSPAN=2><BR>Customer needs more servers for new project. Is doing online order processing for sales reps in the field.<BR></TD>
		</TR>
		</TABLE>
		<BR CLEAR="all">
		<TABLE WIDTH="160" BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD BGCOLOR="#FFCC33" VALIGN="top">
			<IMG SRC="/images/note_corner.gif" WIDTH="10" HEIGHT="10" HSPACE="0" VSPACE="0" BORDER="0" ALIGN="TOP" ALT="">
			&nbsp; belmendo </TD>
			<TD ALIGN="right" BGCOLOR="#FFCC33"> 02/16/2002 4:30pm </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#FFF999" COLSPAN=2><BR>Customer needs more servers for new project. Is doing online order processing for sales reps in the field.<BR></TD>
		</TR>
		</TABLE>
		<BR CLEAR="all">				
		<!-- End Notes ----------------------------------------------------- -->	
	</TD>
</TR>
</TABLE>
</BODY>
</HTML>
