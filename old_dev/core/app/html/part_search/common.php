<?php

require_once("CORE_app.php");
$report_db = getReportDB();

session_register( "SESSION_parts" );
session_register( "SESSION_parts_sla" );
session_register( "SESSION_parts_dc" );
if( empty( $SESSION_parts ) or count($SESSION_parts) <= 0 ) {
    $first_time = true;
} else {
    $first_time = false;
}

if( !isset($sla_type) ) {
    if( !empty($SESSION_parts_sla) ) {
        $sla_type = $SESSION_parts_sla;
    } else {
        $sla_type = 0;
    }
} else {
    $SESSION_parts_sla = $sla_type;
}

if( !isset($dc_type) ) {
    if( !empty($SESSION_parts_dc) ) {
        $dc_type = $SESSION_parts_dc;
    } else {
        $dc_type = 1;
    }
} else {
    $SESSION_parts_dc = $dc_type;
}

if( $dc_type ) {
    $and_datacenter = "AND datacenter_number = $dc_type";
    $show_datacenter = "";
    $name = $report_db->getVal( "select name from datacenter where datacenter_number = $dc_type" );
    $warn_datacenter = "<p style=\"text-align: center; background: #EEF\">Using only datacenter <b>$name</b></p>\n";
} else {
    $and_datacenter = "";
    $show_datacenter = " (' || datacenter.name || ') ";
    $warn_datacenter = "";
}

function PrintSKUTable( $array, $num = -1 ) {
    global $report_db, $SESSION_parts, $and_datacenter, $dc_type;
    
    if( empty($array) or count($array) <= 0 or 
        empty($array['not']) or empty($array['sku']) ) {
        return "ERROR";
    }

    echo "<TABLE BORDER='1' class='skutable'><tr><th>\n";
    
    if( !empty($array['logic']) ) {
        echo "$array[logic] ";
        if( !empty($array['not']) and $array['not'] == 't' ) {
            echo " NOT ";
        }
    } else {
        if( !empty($array['not']) and $array['not'] == 't' ) {
            echo "Servers without... ";
        } else {
            echo "Servers with... ";
        }
    }

    echo "&nbsp;";
    if( $num >= 0 ) {
        echo "(<a href='delete_handler.php?id=$num'>Delete</a>)\n";
    }
    echo "</th></tr>\n";

    $result = $report_db->SubmitQuery('
SELECT
  product_description as name,
  product_name as category,
  product_sku as sku,
  datacenter.name as datacenter
FROM
  product_table
  join datacenter using (datacenter_number)
WHERE
' . makeSKUInList( $array['sku']  ). '
'.$and_datacenter.'
' );

    $rows = $result->numRows();
    for( $i=0; $i<$rows; $i++ ) {
        echo "<tr><td>\n";
        $sku = $result->getResult($i, 'sku');
        $name = $result->getResult($i, 'name');
        $category = $result->getResult($i, 'category');
        if( $dc_type ) {
            $datacenter = "";
        } else {
            $datacenter = "(".$result->getResult($i, 'datacenter').")";
        }

        echo "$category $datacenter [#$sku] $name\n";
        echo "</td></tr>\n";
    }
    echo "</table>";
}

function PrintList( $array, $editable=true ) {
    if( empty( $array) or count($array) <= 0 ) {
        echo "No Query Built<br>\n";
        return;
    }
    
    $table_start = "<table border='0' cellspacing='0' cellpadding='0'><tr>\n";
    $table_end = "</tr></table>\n";
    echo $table_start;
    foreach( $array as $k => $v ) {
        if( !empty($v['logic']) and $v['logic'] == 'OR' ) {
            echo $table_end;
            echo "<hr noshade>\n";
            echo $table_start;
        }
        echo "<td valign='top' nowrap>";
        if( $editable ) {
            PrintSKUTable( $v, $k );
        } else {
            PrintSKUTable( $v );
        }
        echo "</td>";
    }
    echo $table_end;
}

function makeSKUInList( $sku, $not = 'f' ) {
    if( $not == 'f' ) {
        $result = "product_sku in (";
        $result .= join( ",", $sku );
        $result .= ")";
    } else {
        $result = "computer_number not in (select distinct computer_number ";
        $result .= "from server_parts where " . makeSKUInList( $sku ) . ")";
    }

    $result .= "\n";
    return $result;
}

function SLATypeWhere( $type ) {
    $result = 'customer_number in (';
    $result .= 'select "AccountNumber" from "ACCT_Account" ';
    $result .= 'where "ACCT_val_SLATypeID" = '.$type.')';

    return $result;
}

function BuildSQL( $sku, $do_count=true ) {
    global $startdate, $enddate, $sla_type, $and_datacenter;
    $selects = array();
    $count = 0;
    if( ! is_array( $sku ) ) {
        trigger_error( "SKU is not a list! Maybe the database is really busy?: '$sku'",
                       FATAL );
    }
    foreach( $sku as $list ) {
        $count++;
        $select = "select distinct computer_number";
        if( $list['not'] == 't' ) {
            $from = "from server\n";
        } else {
            $from = "from server join server_parts using (computer_number)\n";
        }
        $where = "where ".makeSKUInList( $list['sku'], $list['not'] );
        if( !empty( $startdate ) ) {
            $where .= " AND date(server.sec_created::abstime) >= '$startdate'";
        }
        if( !empty( $enddate ) ) {
            $where .= " AND date(server.sec_created::abstime) <= '$enddate'";
        }

        if( !empty($sla_type) ) {
            $where .= ' AND '.SLATypeWhere( $sla_type );
        }

        $where .= " AND status_number > 7 ";
        $where .= $and_datacenter;

        if( count($sku) == 1 and !$do_count ) {
            $selects[] = "$select $from $where";
        } else {
            if( count($selects) <= 0 ) {
                $selects[] = "($select $from $where) as A$count";
            } else {
                $selects[] = "join ($select $from $where) as A$count using (computer_number)";
            }
        }
    }
    $returns = "";
    if( $do_count ) {
        $returns = "select count(computer_number) from ";
    } elseif( count($selects) > 1 ) {
        $returns = "select computer_number from ";
    }
    $returns .= join( ' ', $selects );

    return $returns;
}

function BuildProductWhere( $group) {

$where = "";
if( !is_array($group) ) {
    return $where;
}
foreach( $group as $list ) {
    if( !empty($where) ) {
        $where .= ' OR ';
    }
    $where .= makeSKUInList( $list['sku'], $list['not'] );
}
$where = "computer_number in (".
   BuildSQL($group, false).
   ") AND ($where)";

return $where;

}

function GroupParts($array) {
    $returns = array();
    $group = array();
    if( !is_array($array) ) {
    	return $returns;
    }
    $count = 0;
    foreach( $array as $k => $v ) {
        if( !empty($v['logic']) and $v['logic'] == "OR" ) {
            if( $count > 0 ) {
                echo "<hr noshade>\n";
            }
            $returns[] = $group;
            $group = array();
            $count++;
        }
        $group[] = $v;
    }

    if( count($group) > 0 ) {
        $returns[] = $group;
    }

    return $returns;
}

function iter( $array, $inc=0 ) {
    foreach( $array as $k => $v ) {
        for( $i=0; $i<$inc ; $i++ ) {
            echo "&nbsp;&nbsp;";
        }
        echo "<b>$k</b>: $v <br>\n";
        if( is_array($v) ) {
            iter($v, $inc + 1);
        }
    }
}

/*
 * This is the timing code
 */
if( isset($print_sql) and !isset($dotime) and in_dept("CORE") ) {
    $dotime = 1;

    $starttime = mtime();
    $lasttime = $starttime;
}

function mtime() {
    $array = explode( " ", microtime() );
    $result = $array[0] + $array[1];
    return $result;
}

function ptime() {
    global $lasttime, $dotime;
 
    if( empty($dotime) ) {
        return;
    }
    $ctime = mtime();
    echo "<br><b>Time: +";
    echo $ctime - $lasttime;
    echo "</b><br>\n";
    $lasttime = $ctime;
    flush();
}

function ttime() {
    global $starttime, $dotime;

    if( empty($dotime) ) {
        return;
    }
    $ctime = mtime();
    print "<br><b>Total Time: +";
    print $ctime - $starttime;
    print "</b><br>\n";
}

$dateargs = "";
if( !empty( $startdate ) ) {
    $dateargs .= "&startdate=$startdate";
}
if( !empty( $enddate ) ) {
    $dateargs .= "&enddate=$enddate";
}

$back_build_link = "<a href='build_page.php?$dateargs'>Re-Build your query</a>";
$back_summary_link = "<a href='summary_page.php?$dateargs'>Back to the summaries</a>";


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
