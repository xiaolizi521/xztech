<?php 
require_once("CORE_app.php");
require_once("menus.php");

$back_url = "/reports/show.php?page=site_submit_report.php";
$title = "Display Site Submission";


function displayNotFoundError($oid) {
    global $title, $back_url;
    ?>
<html id="mainbody">
<head>
<title><?=$title?></title>
<link href="/css/core2_basic.css" rel="stylesheet">
<?= menu_headers() ?>
</head>
<?= page_start() ?>

<p>
<a href="<?=$back_url?>" class="text_button">Back</a>
</p>
<h1>Error:</h1>
<p>
Site submission #<?=$oid?> was not found. It may have been deleted or
converted into an account by someone else. If you do not believe this is the
case, please submit a Core bug report.
</p>
<?
}

$self_url = "display_submission.php3?oid=$oid";
if (!isset($command)) {
    $command="";
}
    $customer_submission="customer_submission";
    if ($command=="DELETE")
    {
        remove_self();
        $result=$db->SubmitQuery("DELETE from customer_submission where oid=$oid::oid;");    
            if (!$result)
            {
                $db->AbortTransaction();
                trigger_error("Couldn't delete customer submission",FATAL);
            }
        $db->CloseConnection();
        Header("Location: $self_url");
        exit;
    }
    else if ($command=="ACTIVE")
    {
        $stamp=time();
        $db->SubmitQuery("UPDATE customer_submission set sec_last_mod=$stamp, status_number=0 where oid=$oid::oid;");

    }
    else if ($command=="DOWNGRADE")
    {
        $stamp=time();
        $db->SubmitQuery("UPDATE customer_submission set sec_last_mod=$stamp, status_number=$status_number where oid=$oid::oid;");

    }
    else if ($command=="ASSIGN_REP")
    {
        $stamp=time();
        $result=$db->SubmitQuery("
            SELECT * 
            FROM customer_submission 
            WHERE oid = $oid::oid
            ");
        if ($result->numRows() < 1) {
            displayNotFoundError($oid);
            exit();
        }
        $customer_number=$result->getResult(0,"customer_number");
        $db->BeginTransaction();
        $db->SubmitQuery("
            DELETE FROM rep_assignment 
            WHERE customer_number = $customer_number
            ");
        $db->SubmitQuery("
            INSERT INTO rep_assignment (
                sec_created, sec_last_mod, rep_number, customer_number
                ) 
            VALUES ($stamp, $stamp, $rep_number, $customer_number)
            "); 
        $db->CommitTransaction();
    } 
function printrow($header, $data, $hilight=false, $shade=false) {
    echo '<tr';
    if( $shade ) {
        echo ' style="background: #CCD"';
    }
    echo '><th';
    if( $hilight ) {
        if( empty($data) ) {
            $style = "color: red";
        } else {
            $style = "color: darkgreen";
        }
    } else {
        $style="";
    }
    if( !$shade ) {
        $style="$style; background: #EEE";
    }
    echo " style=\"$style; max-width: 8ex\">";
    echo $header;
    echo '</th><td>';
    if( empty($data) ) {
        echo "&nbsp;";
    } else {
        echo $data;
    }
    echo '</td></tr>';
    echo "\n";
}

$result=$db->SubmitQuery("
       select * from customer_submission where oid=$oid::oid;");
if ($result->numRows() < 1) {
    displayNotFoundError($oid);
    exit();
}
// Makes all the columns from the result into variables.
$fa = $result->fetchArray(0);
foreach( $fa as $k => $v ) {
    $$k = $v;
}
$result->freeResult();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html id="mainbody">
<head>
<title><?=$title?></title>
<link href="/css/core2_basic.css" rel="stylesheet">
<?= menu_headers() ?>
</head>
<?= page_start() ?>

<p>
<a href="<?=$back_url?>" class="text_button">Back</a>
</p>

<table class="blueman" style="width: auto; float: right">
<tr>
  <th  class="blueman"><?=$title?></th></tr>
<tr  class="odd">
  <td colspan="1"  class="blueman">
<!-- Begin Blueman Table -->

<p>
<?php
if( $status_number < 0 ) {
    $status_text = $db->getVal("select status
       from submission_status where status_number = $status_number");
?>
Status: <?=$status_text?> 
    &nbsp; &nbsp;
<A HREF="display_submission.php3?oid=<?=$oid?>&command=ACTIVE"
   class="text_button">Re-Activate</a>
<?php 
} else {
?>

<form action="display_submission.php3" method="get">
     <input type="hidden" name="oid" value="<?=$oid?>">
     <input type="hidden" name="command" value="DOWNGRADE">
<select name="status_number">
<?php
    $status_info=$db->SubmitQuery("
select status_number,status from submission_status order by status_number desc;
");
$num=$db->NumRows($status_info);
for($i=0;$i<$num;$i++) {
    $sn = $status_info->getResult($i,0);
    $st = $status_info->getResult($i,1);
    if ($status_number == $sn) {
        print "<option value=\"$sn\" selected>$st</option>\n";
    } else {
        print "<option value=\"$sn\">$st</option>\n";
    }
}
$status_info->freeResult();
?>
</select>
<input type="submit" value="Change Status" class="form_button">
</form>
<?php
}
?>
</p>

<p>
<FORM ACTION="display_submission.php3" METHOD="get">
<INPUT TYPE=HIDDEN NAME=oid VALUE="<?=$oid?>">
<INPUT TYPE=HIDDEN NAME=command value="ASSIGN_REP">
<SELECT name=rep_number>
<OPTION VALUE="0"> No Rep </option>
<?php
$current_rep = $db->getVal( "select rep_number from rep_assignment where customer_number = $customer_number" );

$rep_info=$db->SubmitQuery('
SELECT xenc.employee_number,
       "FirstName" || \' \' || "LastName"
FROM
  "xref_employee_number_Contact" xenc,
  employee_dept dept,
  "CONT_Contact" contact,
  "CONT_Person" person
WHERE
-- Limits
      department = \'SALES\'
-- Joins
  AND xenc.employee_number = dept.employee_number
  AND contact."ID" = xenc."CONT_ContactID"
  AND person."ID" = contact."CONT_PersonID"
ORDER by "LastName", "FirstName"
');

$num=$db->NumRows($rep_info);
for ($i=0;$i<$num;$i++) {
    $en = $rep_info->getResult($i, 0);
    $name = $rep_info->getResult($i, 1);

    print "<option value=\"$en\"";
    if( $en == $current_rep ) {
        print " SELECTED";
    }
    print "> $name </option>\n";
}
$rep_info->freeResult();

?>
</select>
<input type="submit" value="Change Sales Rep." class="form_button">
</form>
</p>

<br>
<A HREF="display_submission.php3?command=DELETE&oid=<?=$oid?>"
class="text_button"> Delete Submission </a>
</p>

<!-- End Blueman Table -->
</td></tr></table>

<table border="2" class="rowtable" width="50%" style="float: left">
<tr><th colspan="2" class="blueman"><?=$title?>
&nbsp; &nbsp;
<a href="edit_submission.php3?oid=<?=$oid?>" 
class="text_button"> Edit </a>
</th></tr>
<?php

if( $submitted_to == "myrackspace_referral" ) {
    $status_color = "#CC33CC";
    $submitted_message = "Referral";
} elseif ($submitted_to == "instant_callback") {
    $status_color = "#AA0000";
    $submitted_message = "Callback";
} else {
    $status_color = "#0000AA";
    $submitted_message = "Submission";
}

// If Status Number isn't a number (You cannot use empty() here to detect this)

// as per #030715-0243, they don't want the country abbreviation displayed.
// they want the full name.
// Chile and American Samoa are in the database twice, so we need to get just
// one result.

$country = $GLOBAL_db->GetVal('SELECT distinct("Name") from "CONT_Country" where "Abbrev" = \'' . $country .'\';');

if( $status_number != "" ) {
   $status = $GLOBAL_db->GetVal('
   SELECT status
   FROM submission_status
   WHERE status_number = '.$status_number );
   $status = "<font color=\"$status_color\">$status $submitted_message</font>";

   printrow('First Name:',$first_name,true,true);
   printrow('Last Name:',$last_name,true,true);
   printrow('Email:', $email,true,true);
   
   printrow('Status:', $status);
   printrow('Date Submitted:', strftime("%m/%d/%Y %r",$sec_created) );
   
   printrow('Title:',$title);
   printrow('Company:',$company);
   printrow('Phone:',$phone,true);
   printrow('Fax:',$fax );
   printrow('Street:',$street,true );
   printrow('Street2:',$street2 );
   
   printrow('City:',$city,true);
   printrow('State:',$state,true);
   printrow('Zip Code:',$zipcode,true);
   printrow('Country:',$country,true);
   
   printrow('Submitted To:',$submitted_to );
   printrow('Comments:',$comments );
   printrow('Staff Comments:',$staff_comments );
} else {
   printrow('Status:'," DELETED");
}

?>
<tr>
<td colspan="2">
<br>
<p>Note: all items in <font color="red">Red</font> must have valid values in them before converting to an account. <font color="darkgreen">Green</font> values have already been set to valid values.
</p>
</td>
</tr>
</table>

<br clear="all">
<p>
<a href="<?=$back_url?>" class="text_button">Back</a>
</p>

<? 
        $db->CloseConnection();
?>
<?= page_stop() ?>

</HTML>
