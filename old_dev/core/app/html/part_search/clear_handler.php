<?php

require_once("CORE_app.php");
require_once("common.php");
require_once("helpers.php");

session_unregister( 'SESSION_parts' );

ForceReload( 'build_page.php' );
// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
