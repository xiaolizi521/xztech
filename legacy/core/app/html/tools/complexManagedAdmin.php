<?
require_once("CORE_app.php");
require_once("menus.php");

$mrpp = 10;

function getVLANS($type, $number) {
    global $GLOBAL_db, $page_params;
    $page_params = array();
    $vlan_list = array();
    $query = 'select cm.name, ref.account_number, ref.computer_number, dc.name, dc.datacenter_abbr, cm."NTWK_GroupID", g.name from complexmanaged_vlan cm left join xref_computer_complexmanaged ref using (vlan_id) join "NTWK_Group" g on (cm."NTWK_GroupID" = g.id) join datacenter dc using (datacenter_number)';
    $conditions = array();
    
    if ( isset( $type ) and isset($number) and $number != '' and $number > 0 )
    {
        if ($type == "0") {
            array_push($conditions, "ref.account_number = $number");
        }
        else if ($type == "1") {
            array_push($conditions, "ref.computer_number = $number");
        }
        array_push($page_params, "type=$type&number=$number");
    }
    if ( isset( $GLOBALS['dc_group'] ) and $GLOBALS['dc_group'] > 0 ) {
        array_push($conditions, 'cm."NTWK_GroupID" = ' . $GLOBALS['dc_group']);
        array_push($page_params, "dc_group=" . $GLOBALS['dc_group']);
    }
    
    /* Filter by vlan name:
     * if CM-100 -> filter on %100
     * if CMVLAN100 -> filter on %100
     */
    if ( isset( $GLOBALS['vlan_name'] ) and $GLOBALS['vlan_name'] != '' ) {
        $vlan_name = preg_replace('/\D+/', '', $GLOBALS['vlan_name']);
        array_push($conditions, 'cm.name like \'%' . $vlan_name . '\'');
        array_push($page_params, "vlan_name=" . $GLOBALS['vlan_name']);
    }
    if ( isset ( $GLOBALS['active'] ) ) {
        if ( $GLOBALS['active'] == 1) {
            array_push($conditions, 'ref.account_number is Null');
            array_push($conditions, 'ref.computer_number is Null');
        }
        else if ( $GLOBALS['active'] == 2) {
            array_push($conditions, 'ref.account_number is not Null');
            array_push($conditions, 'ref.computer_number is not Null');
        }
        array_push($page_params, "active=" . $GLOBALS['active']);
    }
    //add conditions to query.
    if ( count($conditions) > 0 ) {
        $query = "$query where " . implode(" and ", $conditions) . " order by regexp_replace(cm.name, '\\\D*', '')::int, 1,2,3,4,5";
    } else {
        $query = "$query order by regexp_replace(cm.name, '\\\D*', '')::int, 1,2,3,4,5";
    }
    
    $result = $GLOBAL_db->SubmitQuery($query);
    for ( $i=0; $i<$result->numRows(); $i++ ) {
        $vlan_list[] = array( $result->getCell($i, 0), $result->getCell($i, 1), $result->getCell($i, 2), $result->getCell($i, 3), $result->getCell($i, 4), $result->getCell($i, 5), $result->getCell($i, 6));
    }
    return $vlan_list;
}

function showVLANs($type, $number, $page) {
    global $mrpp, $GLOBAL_db, $page_params, $dc_group_list;
    
    $dc_group_query = 'select g.id, dc.datacenter_abbr, g.name from "NTWK_Group" g join datacenter dc using (datacenter_number) order by g.id;';
    $result = $GLOBAL_db->SubmitQuery($dc_group_query);
    $dc_group_list = array();
    for ($i=0; $i<$result->numRows(); $i++) {
        $dc_group_list[] = array('group_id'=>$result->getCell($i, 0), 'dc_abbr'=>$result->getCell($i, 1), 'group_name'=>$result->getCell($i, 2));
    }
    $vlan_list = getVLANS($type, $number);
    $lastpage = ceil(count($vlan_list) / $mrpp);
    ?>
    <DIV class="blueman_div_border">
    <SPAN class="blueman_span_title">ComplexManaged VLANs</SPAN>
    <FORM name="searchform" action="complexManagedAdmin.php" method="GET">
        <FIELDSET>
            <LEGEND>Filter</LEGEND>
            <table>
                <tr>
                    <td class="rowtable_td">
                        Datacenter & Group: 
                        <select name="dc_group" id="dc_group">
                            <option value=0>All</option>
                            <?
                            for ($i=0; $i<sizeof($dc_group_list); $i++) {
                            ?>
                            <option value="<?=$dc_group_list[$i]['group_id']?>" <?= $dc_group_list[$i]['group_id'] == $GLOBALS['dc_group'] ? 'SELECTED' : '' ?>><?=$dc_group_list[$i]['dc_abbr']?>/<?=$dc_group_list[$i]['group_name']?></option>    
                            <?
                            }
                            ?>
                        </select>

                        Available:
                        <select name="active" id="active">
                            <option value="0" <?= $GLOBALS['active'] == 0 ? 'SELECTED' : '' ?>>Both</option>
                            <option value="1" <?= $GLOBALS['active'] == 1 ? 'SELECTED' : '' ?>>Available</option>
                            <option value="2" <?= $GLOBALS['active'] == 2 ? 'SELECTED' : '' ?>>Not Available</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="rowtable_td">
                        <select name="type" id="type">
                            <option value="0" <?= $GLOBALS['type'] == 0 ? 'SELECTED' : '' ?>>Customer Number</option>
                            <option value="1" <?= $GLOBALS['type'] == 1 ? 'SELECTED' : '' ?>>Computer Number</option>
                        </select>:
                        <INPUT name="number" type="textfield" value="<?= $GLOBALS['number'] != 0 ? $GLOBALS['number'] : '' ?>" id="number"/>
                        VLAN Name: <input type="text" name="vlan_name" value="<?= $GLOBALS['vlan_name'] ?>" id="vlan_name"/>
                        <INPUT type="submit" class="form_button" value="Filter" name="filter"/> <INPUT type="button" class="form_button" value="Reset" onclick="reset_form()"/>
                    </td>
                </tr>
            </table>
        </FIELDSET>
    </FORM>
    <?  if (count($vlan_list) > $mrpp) { ?>
            <P style="text-align: center; border: solid thick lightgrey">Showing page <?=$page ?> of <?=$lastpage ?></P>
            <P style="text-align: right"> 
        <? if ($page != 1) { ?>
            <A href="complexManagedAdmin.php?page=1&type=<?=$type?>&number=<?=$number?>&<?=implode('&', $page_params)?>" title="First Page"><IMG src="/img/button/navigate/tiny/full_left.gif" border="0"></A>  
            <A href="complexManagedAdmin.php?page=<?=$page-1?>&type=<?=$type?>&number=<?=$number?>&<?=implode('&', $page_params)?>" title="Prev page: <?=$page-1?>"><IMG src="/img/button/navigate/tiny/left.gif" border="0"></A>  
        <? } ?>
        <? if ($page != $lastpage) { ?>
            <A href="complexManagedAdmin.php?page=<?=$page+1?>&type=<?=$type?>&number=<?=$number?>&<?=implode('&', $page_params)?>" title="Next page: <?=$page+1?>"><IMG src="/img/button/navigate/tiny/right.gif" border="0"></A>  
            <A href="complexManagedAdmin.php?page=<?=$lastpage?>&type=<?=$type?>&number=<?$number?>&<?=implode('&', $page_params)?>" title="Last page"><IMG src="/img/button/navigate/tiny/full_right.gif" border="0"></A> 
        <? } ?>
    </P> 
    <? } ?>
    <TABLE style="border-spacing: 1px" class="datatable" onClick="">
        <THEAD>
            <TR style="" class="">
                <TH  class="rowtable_th" width="25">Avail</TH>
                <TH  class="rowtable_th">VLAN Name</TH>
                <TH  class="rowtable_th">Customer</TH>
                <TH  class="rowtable_th">Computer</TH>
                <TH  class="rowtable_th">DC/Group</TH>
            </TR>
        </THEAD>
        <?
        $startrow = ($page-1)*$mrpp;
        $endrow = $startrow+$mrpp;
        $last = array('vlan'=>'', 'customer'=>'', 'datacenter'=>'');
        if ($endrow > count($vlan_list))
            $endrow = count($vlan_list);
        for ($i=$startrow; $i<$endrow; $i++ ) {
            if ( $i%2 == 0 ) 
                $evenodd = "even";
            else
                $evenodd = "odd";
            $name = $vlan_list[$i][0]; 
            $customer = $vlan_list[$i][1]; 
            $computer = $vlan_list[$i][2]; 
            $dc = $vlan_list[$i][3];
            $dc_abbrv = $vlan_list[$i][4];
            $group = $vlan_list[$i][6];

            print "<tr class=\"$evenodd\">";
            if ($customer == '' and $computer == '') {
                print "<td><img src='/img/gumdrops/green.gif'/>";
            }
            else {
                print "<td><img src='/img/gumdrops/red.gif'/>";
            }
            if ($last['vlan'] == $name  and $last['datacenter'] == $dc) {
                if ($last['customer'] == $customer and $last['datacenter'] == $dc) {
                    print "<td colspan=\"2\"></td><td class=\"rowtable_td\">$computer</td>";
                }
                else {
                    $last['customer'] = $customer;
                    $last['datacenter'] = $dc;
                    print "<td></td><td class=\"rowtable_td\">$customer</td><td class=\"rowtable_td\">$computer</td>";
                }
            }
            else {
                $last['vlan'] = $name;
                $last['customer'] = $customer;
                $last['datacenter'] = $dc;
                print "<td class=\"rowtable_td\">$name</td><td class=\"rowtable_td\">$customer</td><td class=\"rowtable_td\">$computer</td>";
            }
            print "<td>$dc_abbrv/$group</td></tr>";
            
//end of function showVLANs
        } ?>
    </TABLE>
<? } 
function showAddVLANForm() { 
    global $dc_group_list;
    ?>
    <P>
    <FORM name="addvlanform" action="complexManagedAdmin.php" method="POST">
        <FIELDSET>
            <LEGEND>Add New ComplexManaged VLAN</LEGEND>
            <table>
                <tr>
                    <td>
                        New VLANs for Datacenter & Group: 
                            <select name="group_num">
                                <?
                                for ($i=0; $i<sizeof($dc_group_list); $i++) {
                                ?>
                                <option value="<?=$dc_group_list[$i]['group_id']?>"><?=$dc_group_list[$i]['dc_abbr']?>/<?=$dc_group_list[$i]['group_name']?></option>    
                                <?
                                }
                                ?>
                            </select>
                        
                        <P style="width: 60ex"><i>The format of the upload file is rows of VLAN names seperated by commas.  It must be plain text.<br> Example Row: <br><code> &nbsp; 110,111,112,113,114,115</code></i></P>
                    </td>
                </tr>            
                <tr>
                    <td>
                        <TEXTAREA rows="10" cols="50" name="newvlans"></TEXTAREA>
                    </td>
                </tr>
                <tr>
                    <td>
                        <INPUT type="submit" class="form_button" value="Add New VLANs">
                    </td>
                </tr>
            </table>
        </FIELDSET>
    </FORM>
<? } 

function processNewVLANs($newvlans) {
    global $GLOBAL_db;
    $nvlans = explode(",", $newvlans);
    $group_id = $GLOBALS['group_num'];
    foreach ($nvlans as $key => $value) {
        $value = trim($value);
        $value = preg_replace('/\D+/', '', $value);
        if ($value)
        {
            $alreadyexists = "select * from complexmanaged_vlan where name like '%$value' and \"NTWK_GroupID\" = $group_id"; 
            $result = $GLOBAL_db->SubmitQuery($alreadyexists);
            if ($result->numRows()==0)
            {
                $sql = "insert into complexmanaged_vlan (name, \"NTWK_GroupID\" ) values('CM-$value', $group_id)";
                // print "SQL: $sql";
                $GLOBAL_db->BeginTransaction();
                $result = $GLOBAL_db->SubmitQuery($sql);
                $GLOBAL_db->CommitTransaction();
            }
        }
    }
} // end of function processNewVLANs 
?>

<HTML id="mainbody">
<HEAD>
<title>ComplexManaged Admin</title>
<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">
<?= menu_headers() ?>
<script>
    function reset_form() {
        document.getElementById('dc_group').selectedIndex = 0;
        document.getElementById('active').selectedIndex = 0;
        document.getElementById('type').selectedIndex = 0;
        document.getElementById('number').value = '';
        document.getElementById('vlan_name').value = '';        
    }
</script>
</HEAD>
<?
print page_start();
if (isset($newvlans)) {
    processNewVLANs($newvlans);
}
if (!isset($type)) {
    $type = 2;
}
if (!isset($page)) {
    $page=1;
}
if (!isset($number)) {
    $number=0;
}
showVLANs($type, $number, $page);
showAddVLANForm();
print page_stop();
?>
</HTML>
