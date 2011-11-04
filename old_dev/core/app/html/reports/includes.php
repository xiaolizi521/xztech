<?php

function getTitle( $file ) {
        $title = $file;
        if( !@is_readable( $file ) ) {
                return $title;
        }

        $count = 0;
        foreach( file($file) as $line ) {
                if( ereg( "^#+ *TITLE: *(.*)$", $line, $regs ) ) {
                        return trim( $regs[1] );
                }
                if( $count++ >= 5 ) {
                        break;
                }
        }

        return $title;
}


?>