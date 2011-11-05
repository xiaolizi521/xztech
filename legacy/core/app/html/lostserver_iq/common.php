<?php
require_once("helpers.php");

function getPhpTitle( $file, $show_date = false ) {
    $title = $file;
    if( !@is_readable( $file ) ) {
        return $title;
    }
    $title = "";

    $count = 0;
    foreach( file($file) as $line ) {
        if( ereg( "^(//|#)+ *TITLE: *(.*)$", $line, $regs ) ) {
            $title = trim( $regs[2] );
            break;
        }
        if( $count++ >= 3 ) {
            break;
        }
    }

    if( $show_date ) {
        $title = ereg_replace( '\$Date: 02/01/16 21:26:12-00:00 $$',
                               '(\1)', $title);
    } else {
        $title = ereg_replace('\$.*\$', '', $title);
    }
    
    return $title;
}

function getSqlTitle( $file, $show_date = false ) {
    $title = $file;
    if( !@is_readable( $file ) ) {
        return $title;
    }
    
    $lines = file($file);
    if( ereg( "^--+ *(.*)$", $lines[0], $regs ) ) {
        $title = trim( $regs[1] );
        
        if( $show_date ) {
            $title = ereg_replace( '\$Date: 02/01/16 21:26:12-00:00 $$',
                                   '(\1)', $title);
        } else {
            $title = ereg_replace('\$.*\$', '', $title);
        }
        
        return $title;
    }
        
    return 0;
}

function sendHeaders( $file ) {
    global $year,$month,$title;
    if( eregi( "_part\.php$", $file ) ) {
        $title = getPhpTitle( $file );
    } elseif( eregi( "\.sql$", $file ) ) {
        $title = getSqlTitle( $file );
    }
    
    $fname = eregi_replace("\.sql|\.php","",$file);
    $fname .= "_$month"."_$year.xls";

    checkDataOrExit( array( "title" => "SQL Report" ) );
    
    Header("Pragma:");
    Header("Content-type: application/vnd.ms-excel");
    Header("Content-Description: CORE $title");
    Header("Content-Disposition: attachment; filename=$fname");
    flush();
}

function getDateWhere($field = 'queue_cancel_server.sec_created') {
    global $month, $year, $when;
    if( empty($month) or
        $month > 12 or 
        $month < 1 or
        empty($year) or
        $year < 1990 ) {
        ForceReload( 'index.php' );
    }

    $start = "$year-$month-1";
    if( $month == 12 ) {
        $end = ($year+1).'-1-1';
    } else {
        $end = "$year-".($month+1)."-1";
    }

    $date_where = "date($field) >= date('$start') 
AND date($field) < date('$end')";

    return $date_where;
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>