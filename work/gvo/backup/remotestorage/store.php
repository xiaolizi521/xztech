<?php

include "includes/cloudfiles.php";
include "includes/class.mysql.php";

/*
** This file is to store files into the GoGVO Cloud Files Storage Containers
** This file is to not be modified.
** 
** Usage:
**
** store.php filename hostip type
**
** Where hostip is xxx.xxx.xxx.xxx
**
** Type is db or src
**
**
*/

if(!$argv[1] || !$argv[2] || !$argv[3]):

    printUsage();
    die();

endif;

$fileanme = $argv[1];
$host = $argv[2];
$type = $argv[3];

$hostquery = "select clocation from hosts where ip = '".$host."'";




# Authenticate to Cloud Files.  The default is to automatically try
# to re-authenticate if an authentication token expires.
#
# NOTE: Some versions of cURL include an outdated certificate authority (CA)
#       file.  This API ships with a newer version obtained directly from
#       cURL's web site (http://curl.haxx.se).  To use the newer CA bundle,
#       call the CF_Authentication instance's 'ssl_use_cabundle()' method.
#
$auth = new CF_Authentication("gogvo", "2c6cf4d0fd9ab4afe478db1353d0bd24");
# $auth->ssl_use_cabundle();  # bypass cURL's old CA bundle
$auth->authenticate();

# Establish a connection to the storage system
#
# NOTE: Some versions of cURL include an outdated certificate authority (CA)
#       file.  This API ships with a newer version obtained directly from
#       cURL's web site (http://curl.haxx.se).  To use the newer CA bundle,
#       call the CF_Connection instance's 'ssl_use_cabundle()' method.
#
$conn = new CF_Connection($auth);
# $conn->ssl_use_cabundle();  # bypass cURL's old CA bundle

# Create a remote Container and storage Object
#
$images = $conn->get_container("photos");
$bday = $images->create_object("first_birthday.jpg");

# Upload content from a local file by streaming it.  Note that we use
# a "float" for the file size to overcome PHP's 32-bit integer limit for
# very large files.
#
$fname = "/home/user/photos/birthdays/birthday1.jpg";  # filename to upload
$size = (float) sprintf("%u", filesize($fname));
$fp = open($fname, "r");
$bday->write($fp, $size);

# Or... use a convenience function instead
#
$bday->load_from_filename("/home/user/photos/birthdays/birthday1.jpg");

# Now, publish the "photos" container to serve the images by CDN.
# Use the "$uri" value to put in your web pages or send the link in an
# email message, etc.
#
$uri = $images->make_public();

# Or... print out the Object's public URI
#
print $bday->public_uri();

?>