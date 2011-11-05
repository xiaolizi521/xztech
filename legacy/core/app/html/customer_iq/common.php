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
        $title = ereg_replace( '\$Date: (.*) \$$',
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
            $title = ereg_replace( '\$Date: (.*) \$$',
                                   '(\1)', $title);
        } else {
            $title = ereg_replace('\$.*\$', '', $title);
        }
        
        return $title;
    }
        
    return 0;
}

function sendHeaders( $file ) {
    global $title;
    if( eregi( "_part\.php$", $file ) ) {
        $title = getPhpTitle( $file );
    } elseif( eregi( "\.sql$", $file ) ) {
        $title = getSqlTitle( $file );
    }
    
    $fname = eregi_replace("\.sql|\.php","",$file);
    $fname .= ".xls";

    checkDataOrExit( array( "title" => "SQL Report" ) );
    
    Header("Pragma:");
    Header("Content-type: application/vnd.ms-excel");
    Header("Content-Description: CORE $title");
    Header("Content-Disposition: attachment; filename=$fname");
    flush();
}

function getQuestionList( $survey_number, $not_in_this="" ) {
    global $db;
    if( !empty( $not_in_this ) ) {
        $clause = "AND question_number not in ($not_in_this)";
    } else {
        $clause = "";
    }
    $request = $db->SubmitQuery(
"
SELECT distinct question_number
FROM survey_cust_questions
WHERE survey_number = $survey_number
  $clause
" );
    $list = array();
    for( $i=0; $i<$request->numRows(); $i++ ) {
        $qn = $request->getCell($i,0);
        $list[] = $qn;
    }
    $numbers = join(", ", $list);
    return $numbers;
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>