<?php

require_once("common.php");
require_once("helpers.php");

checkDataOrExit( array( 'id' => 'Cannot delete without an ID' ) );

if( !empty( $SESSION_parts[$id] ) ) {
    $new = array();

    ksort($SESSION_parts);
    foreach( $SESSION_parts as $k => $v ) {
        if( $k != $id ) {
            $new[] = $SESSION_parts[$k];
        }
    }
    if( !empty($new[0]) ) {
        unset( $new[0]['logic'] );
    }
    $SESSION_parts = $new;
}

# The docs says this should exist.  But it doesn't.
#session_write();

ForceReload( 'build_page.php' );
// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
