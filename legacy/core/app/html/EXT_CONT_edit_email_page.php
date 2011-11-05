<?PHP
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$i_contact = ActFactory::getIContact();
$contact_info = $i_contact->getIndividual($individual_id);

$invalid = false;

if( !empty( $email_value ) ) {
    $email_value = trim($email_value);
    if( !VerifyEmailAddress($email_value) ) {
        $invalid = true;
    }
    if( !$invalid ) {
        $submitted = true;    

	$i_contact->updateExternalContactEmail(
		GetRackSessionContactID(),
		$individual_id,
		$email_value,
		$email_type_id,
		$primary_flag,
		$email_type_id
		);
    }
}


if( !empty($submitted) ) {
    print '<script language="javascript">try { opener.location.reload(true) } catch(e) { } window.close()</script>';
    exit();
}

if( empty($email_value) ) {
    makevar( 'email', $email_address );
} else {
    makevar( 'email', $email_value );
}

        
$hidden_tags  = '<input type="hidden" name="email_address"' .
                ' value="'. $email_address . '">';
$hidden_tags  .= '<input type="hidden" name="email_type_id"' .
                ' value="'. $email_type_id . '">';
$hidden_tags  .= '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';
$hidden_tags  .= '<input type="hidden" name="primary_flag"' .
                ' value="'. $primary_flag . '">';                

?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Edit Email for 
	<?=$contact_info->firstName?> <?=$contact_info->lastName?>
    </TITLE>
	<LINK HREF="/css/core_popup.css" REL="stylesheet">
</HEAD>
<BODY MARGINWIDTH="0" 
      MARGINHEIGHT="0" 
	  LEFTMARGIN=0 
	  TOPMARGIN=0 
	  BGCOLOR="#FFFFFF">

<TABLE BORDER="0" 
	   CELLSPACING="0" 
	   CELLPADDING="4" 
	   ALIGN="left">
<TR>
	<TD>
<!-- Begin Left Content Area ----------------------------------------------  -->
<!-- Begin Edit Email ------------------------------------------------------ -->
	<TABLE BORDER="0" 
	       CELLSPACING="0" 
		   CELLPADDING="0" 
		   ALIGN="left">
	<TR>
		<TD><IMG src="/images/tbl-left-top.jpg" 
		         WIDTH="20" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
		<TD BGCOLOR="#003399" 
		    CLASS="hd3rev"> Edit Email for 
		    <?=$contact_info->firstName?> <?=$contact_info->lastName?>
		    </TD>
		<TD><IMG src="/images/tbl-right-top.jpg" 
		         WIDTH="10" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
	</TR>
	<TR>
		<TD BACKGROUND="/images/tbl-left-tile.jpg"> &nbsp; </TD>
		<TD>
                    <FORM action="EXT_CONT_edit_email_page.php">
                    <?=$hidden_tags ?>
			<TABLE>
			<TR>
				<TD>
                  <?php
        if( empty($email_value) ) {
            echo '<font color="#f09000">';
        } elseif( $invalid ) {
            echo '<font color="#FF0000">';
        } else {
            echo '<font color="black">';
        }
        echo "Email Address:</font>";

                   ?> <INPUT TYPE="text"
				    SIZE="24"
                                    <?=$email_tags ?>
                                    >
				</TD>
			</TR>

            <TR>
				<TD><?php if( $invalid ): ?>
                   <center><font color="#FF0000">Invalid Email Address</font></center>
                   <?php else: echo "&nbsp;"; endif; ?></TD>
			</TR>
			<TR>
				<TD ALIGN="right"> <INPUT TYPE="image" 
				                          NAME="CONTINUE" 
                                                          SRC="/images/button_command_save_off.jpg" 
                                                          HSPACE="2" 
                                                          VSPACE="2" 
                                                          BORDER="0"> </TD>
			</TR>
			</TABLE>
			</FORM>
		</TD>
	</TR>
	<TR ALIGN="left" 
	    VALIGN="top" 
		BGCOLOR="#003399">
		<TD ALIGN="left" 
		    VALIGN="bottom"><IMG src="/images/tbl-left-bottom.jpg" 
			                     WIDTH="20" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
		<TD BGCOLOR="#003399" 
		    class="smhd">&nbsp;</TD>
		<TD HEIGHT="8" 
		    ALIGN="left" 
			VALIGN="bottom"><IMG src="/images/tbl-right-bottom.jpg" 
			                     WIDTH="10" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
	</TR>
	</TABLE>
<!-- End Edit Email -------------------------------------------------------- -->
<!-- End Left Content Area ------------------------------------------------  -->
	</TD>
	<TD VALIGN="top">
<!-- Begin Right Content Area ---------------------------------------------  -->
				
<!-- End Right Content Area -----------------------------------------------  -->
	</TD>
</TR>
</TABLE>

</BODY>
</HTML>
