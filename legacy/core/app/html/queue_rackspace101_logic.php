<? 
    require_once("act/ActFactory.php");

// These need to be isset()s, not empty()s
if( !isset($count_limit) ) {
    $count_limit = 15;
}

if( !isset($date_limit) ) {
    $date_limit = date("m/d/Y", mktime(0,0,0,date("m")-1,date("d"),date("Y")));
}

if( !isset($team_limit) ) {
    $team_limit = "";
}

function PrintOneTeam( $id, $name ) {
    global $GLOBAL_db, $team_limit, $date_limit, $count_limit, $PHP_SELF;

    $i_account = ActFactory::getIAccount();
    $result = $i_account->getRackspace101Info($GLOBAL_db, $id, $date_limit, $count_limit);

    $count = $result->numRows();

    echo "<table class=datatable>\n";
    echo " <tr>\n";
    echo "  <th colspan='4'>\n";
    echo "    &nbsp; ";

    if( empty($team_limit) ) {
        $args = "count_limit=$count_limit&date_limit=$date_limit&team_limit=$id";
        echo "<a href='$PHP_SELF?$args'>$name</a> ";
    } else {
        $args = "count_limit=$count_limit&date_limit=$date_limit";
        echo "$name (<a href='$PHP_SELF?$args'>Show All Teams</a>) ";
    }
    echo " ($count) ";
    echo "\n";
    echo "  </th>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo "  <th>&nbsp;</th>\n";
    echo "  <th>";
    echo "  Age (days)\n";
    echo "  </th>\n";
    echo "  <th>\n";
    echo "   Online Date\n";
    echo "  </th>\n";
    echo "  <th>\n";
    echo "   Acct #\n";
    echo "  </th>\n";
    echo " </tr>\n";

    for($i=0; $i<$count; $i++) {
        $anum = $result->getCell($i, 'customer_number');
        $cnum = $result->getCell($i, 'computer_number');
        $date = $result->getCell($i, 'Date');
        $age  = $result->getCell($i, 'age');
        if( $i % 2 ) {
            $css = "odd";
        } else {
            $css = "even";
        }
        if( $age >= 7 ) {
            $css .= "red";
        }
        $ctr = $i+1;
        echo " <tr class='$css'>\n";
        echo "  <td class=counter> $ctr </td>\n";
        echo "  <td align='center'>\n";
        echo "    $age\n";
        echo "  </td>\n";
        echo "  <td align='center'>\n";
        echo "    $date\n";
        echo "  </td>\n";
        echo "  <td align='center'>\n";
        echo "   <a href=\"/ACCT_main_workspace_page.php?content_page.php=/py/account/view.pt&account_number=$anum&customer_number=$anum\">$anum</a>\n";
        echo "  </td>\n";
        echo " </tr>\n";
    }
    echo "</table>\n";
    flush();
}

function PrintQueue() {
    global $GLOBAL_db, $team_limit;

    $i_account = ActFactory::getIAccount();
    $result = $i_account->getAccountTeamInfo($GLOBAL_db, "");
    $teams = array();
    for($i=0; $i<$result->numRows(); $i++ ) {
        $teams[$result->getCell($i, 'team_id')] = $result->getCell($i, 'team_name');
    }
     
    foreach( $teams as $id => $name ) {
        PrintOneTeam( $id, $name );
        echo "<br>\n";
    }

}   



// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
