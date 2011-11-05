<?php
require_once("CORE_app.php");
require_once("helpers.php");

if (!isset($submitted_to)) {
    $submitted_to='';
}
if(!empty($command) and $command=="EDIT_SUBMISSION") {
    $stamp=time();

    $first_name = trim($first_name);
    $last_name = trim($last_name);
    $email = trim($email);
    $phone = trim($phone);
    $fax = trim($fax);
    $zipcode = trim($zipcode);
    $city = trim($city);
    
    $db->SubmitQuery("
UPDATE customer_submission 
SET sec_last_mod=$stamp,
    first_name='$first_name',
    last_name='$last_name',
    email='$email',
    title='$title',
    company='$company',
    street='$street',
    street2='$street2',
    city='$city',
    state='$state',
    country='$country',
    zipcode='$zipcode',
    phone='$phone',
    fax='$fax',
    comments='$comments',
    staff_comments='$staff_comments' 
WHERE oid=$oid;");

    $db->CloseConnection();
    Header("Location: display_submission.php3?oid=$oid\n\n");
    exit;
}
?>
<HTML>
<HEAD>
<TITLE>Edit Web Submission</TITLE>
</HEAD>
<?php
require_once("tools_body.php");
?>

<TABLE CELLSPACING=0 CELLPADDING=0 VALIGN="TOP" BORDER=0 WIDTH=540>
<!-- end spacer -->

<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" FACE="Arial"><CENTER>Edit Site Submission</CENTER></FONT></TD>
</TR>
<!-- spacer -->
<TR>
	<TD>&nbsp;</TD>
</TR>
<?
	//Load up the customer profile and status

	$customer_info=$db->SubmitQuery("SELECT * from customer_submission where oid=$oid;");
	
?>
	<TD BGCOLOR="#C0C0C0"><B>&nbsp;&nbsp;Customer Info:</B></TD>
	<TD>&nbsp;&nbsp;
</TR>
<TR>
<TD COLSPAN=2 ALIGN="CENTER">
<FORM ACTION="edit_submission.php3" METHOD="POST">
<TABLE BORDER="0">
  <INPUT TYPE=HIDDEN name=command value="EDIT_SUBMISSION">
  <INPUT TYPE=HIDDEN name=oid value="<?print($oid);?>">
<?php
function printrow($header, $data, $hilight=0) {
  global $customer_info;
  echo '<tr><th align="left">';

  $value = $customer_info->getHTResult(0,$data);

  if( $hilight ) {
    if( empty($value) ) {
      $color = "red";
    } else {
      $color = "darkgreen";
    }
    echo '<font color="'.$color.'">';
    echo $header;
    echo '</font>';
  } else {
    echo $header;
  }
  echo '</th><td>';

  if( $data == "country" ) {
    echo BuildCountrySelect("country",$value);
  } else {
    echo '<input type="text" name="'.$data.'" size="50" value="'
      .$value.'">';
  }
  echo '</td></tr>';
  echo "\n";
}

printrow("First Name:","first_name");
printrow('Last Name:',"last_name",true);
printrow('Email:',"email",true);

printrow('Title:',"title");
printrow('Company:',"company");
printrow('Phone:',"phone",true);
printrow('Fax:',"fax");
printrow('Street:',"street",true);
printrow('Street2:',"street2");

printrow('City:',"city",true);
printrow('State:',"state",true);
printrow('Zip Code:',"zipcode",true);
printrow('Country:',"country",true);


  
  ?>

<TR><TH ALIGN=LEFT >Comments:</TH></TR>
<TR><TD ALIGN=LEFT COLSPAN=2><TEXTAREA NAME="comments" COLS=50 ROWS=4 WRAP=VIRTUAL><?print($customer_info->getResult(0,"comments"));?></TEXTAREA></TD></TR>
<TR><TH ALIGN=LEFT >Staff Comments:</TH></TR>
<TR><TD ALIGN=LEFT COLSPAN=2><TEXTAREA NAME="staff_comments" COLS=50 ROWS=4 WRAP=VIRTUAL><?print($customer_info->getResult(0,"staff_comments"));?></TEXTAREA></TD></TR>
  <tr>
    <td colspan="2">
      <br>
      <p>Note: all items in <font color="red">Red</font> must have valid values in them before converting to an account. <font color="darkgreen">Green</font> values have already been set to valid values.
      </p>
    </td>
  </tr>
</TABLE>
</TD></TR>
<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="Submit Changes">
</FORM></TD></TR>
</TABLE>

<TABLE WIDTH="540" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=3 HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>

</TABLE><BR CLEAR="ALL">

<?php
		$customer_info->freeResult();
		$db->CloseConnection();
?>

</BODY>
</HTML>
