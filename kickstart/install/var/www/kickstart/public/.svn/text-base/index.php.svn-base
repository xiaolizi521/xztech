<?php
/**
 * Recoil
 *
 * This is the entry point for all requests made to the Recoil system. Bootstrap
 * initiation and a bit of useful logic for controller testing can be found here.
 *
 * This index has been modified to work when the application is installed on
 * /opt/recoil.
 * @category  Kickstart
 * @copyright Copyright (c) 2008, PEER 1 Network Enterprises, Inc.
 * @Link      https://wiki.peer1.com/index.php/Kickstart
 * @version   $Id: 1.000 12/02/2008 cavila$
 */

/**
 *  APPLICATION_PATH is a constant pointing to our
 *  application/subdirectory. We use this to add our "library" directory
 *  to the include_path, so that PHP can find our Zend Framework classes.
 *  APPLICATION CONSTANTS - Set the constants to use in this application.
 *  These constants are accessible throughout the application, even in ini files.
 */
define('APPLICATION_ENVIRONMENT', 'development');
define('APPLICATION_PATH', '/opt/recoil/application/' );
define('CONFIG_PATH', APPLICATION_PATH . '/config/config.xml');
set_include_path( APPLICATION_PATH . '/../library' . PATH_SEPARATOR . get_include_path() );

// AUTOLOADER - Set up autoloading.
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

try {
        // REQUIRE APPLICATION BOOTSTRAP: Perform application-specific setup
        require_once ( APPLICATION_PATH . '/bootstrap.php' );
        /**
         * Dispatch the request using the front controller.
         * The front controller is a singleton, and should be setup by now. We
         * will grab an instance and call dispatch() on it, which dispatches the
         * current request.
         */
        Zend_Controller_Front::getInstance()->dispatch();

} catch (Exception $exception) {
        $message = $exception->getMessage();
        $stack_trace = $exception->getTraceAsString();
        $page   = Array();
        if ( preg_match( '@^/upi.*@i',  $_SERVER['REQUEST_URI'] ) ){
                // UPI MODULE - Handle the exception for the upi module
                header( 'Content-Type: text/xml' );
                $page[] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                $page[] = "<exception>";
                $page[] = "\t<message>{$message}</message>";
                if ( APPLICATION_ENVIRONMENT != 'production' ) {
                        $page[] = "\t<stack>";
                        foreach ( explode( "\n", $stack_trace ) as $line ){
                                $element = substr( $line, 1, 1 );
                                $page[] = "\t<step id=\"{$element}\">{$line}</step>";
                        }
                        $page[] = "\t</stack>";
                }
                $page[] = "\t<status>failed</status>";
                $page[] = "</exception>";

        } else {
                // DEFAULT MODULE - Handle the exception for the default module
                $page[] = "<html>\n<body>\n<center>";
                $page[] = 'An exception occured while bootstrapping the application.';
                if ( APPLICATION_ENVIRONMENT != 'production' ) {
                        $page[] = "<br/><br/>{$message}<br/>";
                        $page[] = "<div align=\"left\">Stack Trace:";
                        $page[] = "<pre>{$stack_trace}</pre></div>";
                }
                $page[] = "</center>\n</body>\n</html>";
        }
        print join( "\n", $page );
        exit(1);
}
