<? 
include("CORE.php");
include("license.phlib");

############################
# Contents:                #
# 1. Function definitions. #
# 2. Constants defined.    #
# 3. Form processed.       #
# 4. Form displayed.       #
############################

if(!isset($license_index))
{
    DisplayError("License index missing.");
}

$license_group = new LicenseGroup($db, $license_index);

$title = "Display License Usage <br>\"" 
    . $license_group->get('license_name') . '"';
require_once("menus.php");

$usage = $license_group->getLicenseUsage();
?>
<html id="mainbody">
<head>
  <title><?= $title ?></title>
  <LINK HREF="/css/core2_basic.css" REL="stylesheet">
  <LINK HREF="/css/core_ui.css" REL="stylesheet">
  <?=menu_headers()?>
</head>
<?=page_start()?>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> <?=$title?> </TD>
         		</TR>
               <TR>
                  <TD> 
<TABLE BORDER=0>
<?

$license_type = $license_group->getLicenseType();

print("<TR><TH align=left> License Type: </TH><TD>" 
    . $license_type['name'] . "</TD>\n");
if ($license_group->get('datacenter_number') > 0)
{
    $dc_name = $db->GetVal("
        select
            name
        from datacenter
        where datacenter_number = " 
            . $license_group->get('datacenter_number'));
    print("<TR><TH align=left>Datacenter where license is valid:</TH>");
    print("<TD>$dc_name</TD>\n");
}

if ($license_type['id'] != 'SITE_LICENSE')
{
    ?>
    <TR><TH align=left>Number of licenses purchased:</TH><TD>
            <?print($license_group->get('license_count'));?> 
        </TD>

    <?
    if ($license_group->get('key_based') == 't') {
        $online_assigned_count = $license_group->getActiveKeyAssignmentCount();
        $offline_assigned_count = 
            $license_group->getInactiveKeyAssignmentCount();

        if ($license_group->get('recyclable_key') == 't') {
            $unassigned_count = $license_group->getUnassignedKeyCount();
        }
        else {
            $unassigned_count = 0;
        }
        $assigned_count = $online_assigned_count + $offline_assigned_count;
        $total_key_count = $assigned_count + $unassigned_count;
        ?>
        <?if ($license_group->get('recyclable_key') == 't'): ?>
            <TR><TH align=left>Number of keys in pool:</TH>
                    <TD><?print($total_key_count);?>
        <?endif;?>

        <TR><TH align=left>Number of keys assigned:</TH>
                <TD>
        <?
        print($assigned_count);
    }
    ?>

    <TR><TH align=left>Total products used:</TH><TD>

    <?
    $total = 0;
    foreach($usage as $key => $value)
    {
        foreach($value as $k2 => $v2)
        {
            $total += $v2['count'];
        }
    }
    print("<font color=red>$total</font></TD></TR>");
}

?>
</TABLE>
<?
foreach($usage as $key => $value)
{
    foreach($value as $k2 => $v2)
    {
        print "<TABLE BORDER=0 WIDTH=100%>\n";
        if($k2 == 'datacenter_name')
        {
            print("<TR><TD CLASS=label BGCOLOR=#003399>");
            print("<font color=white> $v2 </font></TD></TR>\n");
        }
        else
        {
            print("<TR><TD WIDTH=\"20%\" bgcolor=#cccccc> $k2 </TD>\n");
            print("<TD WIDTH=\"70%\" bgcolor=#F0F0F0>" 
                . $v2['product_description'] . "</TD>\n");
            print("<TD WIDTH=\"10%\" bgcolor=#99FF99 align=center>" . $v2['count'] . "</TD></TR>\n");
            if ($key != '')
            {
                if ($license_group->get('datacenter_number') > 0
                    && $license_group->get('datacenter_number') != $key)
                {
                    // Wrong datacenter
                    $server_list = $db->SubmitQuery("
                        select t1.computer_number
                        from server_parts t1, server t2
                        where t1.product_sku = $k2
                            and status_number >= " . STATUS_ONLINE . "
                            and t2.datacenter_number = $key
                            and t1.computer_number = t2.computer_number
                        order by computer_number
                        ");
                    if ($server_list->numRows() > 0)
                    {
                        print("<TR><TD colspan=3 CLASS=label BGCOLOR=#CCCCCC>");
                        print("Unauthorized Servers 
                            - Wrong Datacenter</TH></TR>\n");
                    }
                    print("<TR><TD colspan=3><tt>");
                    for ($k = 0; $k < $server_list->numRows(); $k++)
                    {
                        $computer_number = $server_list->getResult($k, 0);
                        print("<a href=display_computer.php3?"
                            . "computer_number=$computer_number>"
                            . "$computer_number</a>  ");
                    }
                }
                if($license_group->get('key_based') == 't')
                {
                    $server_list = $db->SubmitQuery("
                        select t1.computer_number
                        from server_parts t1, 
                            server t2
                        where t1.product_label not in
                                (select product_label
                                from license_key_assignments sub
                                where active = 't'
                                    and t1.computer_number = sub.computer_number
                                    and t1.product_sku = sub.product_sku
                                )
                            and t1.product_sku = $k2
                            and t1.computer_number = t2.computer_number
                            and t2.datacenter_number = $key
                            and status_number >= " . STATUS_ONLINE . "
                        order by computer_number
                        limit 100
                        ");
                    if ($server_list->numRows() > 0)
                    {
                        print("<TR><TD colspan=3 CLASS=label BGCOLOR=#CCCCCC>");
                        print("Unauthorized Servers 
                            - Missing Keys</TD></TR>\n");
                    }
                    print("<TR><TD colspan=3><tt>");
                    for ($k = 0; $k < $server_list->numRows(); $k++)
                    {
                        $computer_number = $server_list->getResult($k, 0);
                        print("<a href=display_computer.php3?"
                            . "computer_number=$computer_number>"
                            . "$computer_number</a> | ");
                    }
                }
            }
        }
        print("</TD></TR>\n");
        print "</TABLE>\n";
        flush(1);
    }
}
?></TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<BR CLEAR="all">
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> 
                  All Servers Containing this License's Products </TD>
         		</TR>
               <TR>
                  <TD>
                  <?
                  $computer_result = $license_group->getAllComputerNumbers();
                  for ($i = 0; $i < $computer_result->numRows(); $i++) {
                      $computer_number = $computer_result->getResult($i, 0);
                      print "<A HREF=DAT_display_computer.php3?computer_number=$computer_number>"
                          . "$computer_number</A><BR>\n";
                  }
                  $computer_result->freeResult();
                  
                  loadLicenseTree($license_index, false);
                  ?>                 
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<?=page_stop()?>
</HTML>
