<?php

require_once("CORE_app.php");
require_once("common.php");

if( empty($month) ) {
    $month = strftime("%m");
}

$this_year=strftime("%Y");
if( empty($year) ) {
    $year = $this_year;
}

function printList() {
    global $year, $month;
    global $SCRIPT_FILENAME;
    $dir = ereg_replace( "/[^/]*$", "", $SCRIPT_FILENAME );
    $dirobj = dir( '.' );
    $list = array();
    $args = "year=$year&month=$month";
    while( $file = $dirobj->read() ) {
        $title = "";
        $url = "";

        if( eregi( "^[^\.].*_part\.php$", $file ) ) {
            $title = getPhpTitle( $file, 1 );
            $url = "$file?$args";
        } elseif( eregi( "^[^\.].*\.sql$", $file ) ) {
            $title = getSqlTitle( $file, 1 );
            $url = "show_sql.php?sql=$file&$args";
        }
        
        if( !empty($title) ) {
            $list[$title] = $url;
        }
    }
    
    ksort($list);
    foreach( $list as $title => $file ) {
        print "<a href=\"$file\">";
        print "<IMG SRC=\"/images/icon-msexcel2000-32.gif\" 
                    WIDTH=\"32\" 
                    HEIGHT=\"32\" 
                    BORDER=\"0\" 
                    ALT=\"Excel File\"
                    VSPACE=\"2\"
                    ALIGN=\"MIDDLE\"></A>";
        print "&nbsp; &nbsp;<STRONG> $title </STRONG><BR>\n";
    }
    echo "</ul>\n";

}

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>