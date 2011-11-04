<?php

require_once("CORE_app.php");
require_once("CORE_app.php");
require_once("common.php");

function printList() {

    /* printList() basically builds a list of available reports as an
     * associative array in $list. The first part is built from a list
     * of files, the second is built dynamically for each survey.
     *
     * Finally the list is printed for the user.
     */
     

    // Survey Report Information
    $name_prefix = "Survey Tool: ";
    $survey_file = "survey_tool.php";
     
    // Build customer intel reports from directory listing of PHP files
    global $SCRIPT_FILENAME;
    $dir = ereg_replace( "/[^/]*$", "", $SCRIPT_FILENAME );
    $dirobj = dir( '.' );
    $list = array();
    while( $file = $dirobj->read() ) {
        $title = "";
        $url = "";

        if( eregi( "^[^\.].*_part\.php$", $file ) ) {
            $title = getPhpTitle( $file, 1 );
            $url = $file;
        } elseif( eregi( "^[^\.].*\.sql$", $file ) ) {
            $title = getSqlTitle( $file, 1 );
            $url = "show_sql.php?sql=$file";
        }
        
        if( !empty($title) && $title != $survey_file) {
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
//    echo "</ul>\n";

}

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>