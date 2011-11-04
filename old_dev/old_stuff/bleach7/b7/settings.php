<?

processSettings();
function processSettings() {
  global $settings;
  global $store;
  global $seeder;
?>
<html>
<head>
<title>Blog Torrent Upload</title>
<link rel="stylesheet" type="text/css" href="basic.css" />
</head>
<body>
<div id="rap">
<h1 style="margin-bottom: 3px;">Blog Torrent Settings</h1>

<IFRAME FRAMEBORDER=0 style="border: none; padding: 0; margin: 0;" width="100%" height=40 border=0 SRC="http://www.blogtorrent.com/updates/092/">
For updates to Blog Torrent, visit <a href="http://www.blogtorrent.com/">BlogTorrent.com</a>.
</IFRAME>

<?php

if (!is_admin())
     die('Sorry, you must be an admin to access this page');

if (isset($_GET['delete'])) {
  delete_user($_GET['delete']);
  echo "<p><b>User Deleted</b></p>";
}

if (isset($_POST['title'])) {
  $title = $_POST['title'];
} else {
  $title = $settings['Title'];
}
if (isset($_POST['description'])) {
  $description = $_POST['description'];
} else {
  $description = $settings['Description'];
}

if (isset($_POST['mysql_host'])) {
  $mysql_host = $_POST['mysql_host'];
} else {
  $mysql_host = isset($settings['mysql_host'])?$settings['mysql_host']:'';
}
if (isset($_POST['mysql_username'])) {
  $mysql_username = $_POST['mysql_username'];
} else {
  $mysql_username = isset($settings['mysql_username'])?$settings['mysql_username']:'';
}
if (isset($_POST['mysql_database'])) {
  $mysql_database = $_POST['mysql_database'];
} else {
  $mysql_database = isset($settings['mysql_database'])?$settings['mysql_database']:'';
}
if (isset($_POST['mysql_password'])) {
  $mysql_password = $_POST['mysql_password'];
} else {
  $mysql_password = isset($settings['mysql_password'])?$settings['mysql_password']:'';
}

if (isset($_POST['sharing_python'])) {
  $sharing_python = $_POST['sharing_python'];
} else {
  $sharing_python = isset($settings['sharing_python'])?$settings['sharing_python']:'';
}

if (count($_POST)>0) {
  $nonauthupload = ((isset($_POST['nau'])) && ($_POST['nau']=="1"));
  $ping = ((isset($_POST['ping'])) && ($_POST['ping']=="1"));
  $autoauthcanupload = ((isset($_POST['aacu'])) && ($_POST['aacu']=="1"));

  //stop all shares if shutting off sharing
  $sharing_enable = ((isset($_POST['sharing_enable']) && ($_POST['sharing_enable']=="1")));
  $sharing_auto = ((isset($_POST['sharing_auto']) && ($_POST['sharing_auto']=="1")));
  
  $hidenonseed = ((isset($_POST['hidens']) && ($_POST['hidens']=="1")));
} else {
  $nonauthupload = $settings['NonAuthUpload'];
  $autoauthcanupload = $settings['AutoAuthCanUpload'];
  $ping = $settings['Ping'];
  $sharing_enable = $settings["sharing_enable"];
  $sharing_auto = $settings["sharing_auto"];
  $hidenonseed = $settings['HideNonSeed'];
}

$newsettings = array();
$newsettings['Title'] = $title;
$newsettings['Ping'] = $ping;
$newsettings['Description'] = $description;
$newsettings['NonAuthUpload'] = $nonauthupload;
$newsettings['AutoAuthCanUpload'] = $autoauthcanupload;
$newsettings['HideNonSeed'] = $hidenonseed;
$newsettings['mysql_host'] = $mysql_host;
$newsettings['mysql_database'] = $mysql_database;
$newsettings['mysql_username'] = $mysql_username;
$newsettings['mysql_password'] = $mysql_password;
$newsettings['sharing_enable'] = $sharing_enable;
$newsettings['sharing_auto'] = $sharing_auto;
$newsettings['sharing_python'] = $sharing_python;

//Stop seeding everything if sharing is turned off
if ($settings['sharing_enable'] && !$newsettings['sharing_enable']) {
  $seeder->stop_seeding();
}

//If the python field has changed, find the python interpretter again
if ($settings['sharing_python'] != $newsettings['sharing_python']) {
  $newsettings['sharing_actual_python'] = '';
  $settings['sharing_actual_python'] = '';
  $seeder->setup();
}

//Used to determine if we need to re-setup server side seeding after a
//settings change
$was_sharing = $settings['sharing_enable'];

if (count($_POST)>0) {
  echo "<p>Changes Saved</p>";
  save_settings($newsettings);

  //Automagically change the backend if the database settings have changed
  if (strlen($newsettings['mysql_database'])) {
    if ($store->type() != 'MySQL') {
      $temp = new MySqlStore();
      if ($temp->setup()) {
	$store = $temp;
	$store->addFlatFileTorrents();
      }
    }
  } else {
    if ($store->type() == 'MySQL') {
      $temp = new FlatFileStore();
      if ($temp->setup())
	$store = $temp;
    }
  }

  //Re-setup sharing if the setting has changed
  if ($settings['sharing_enable'] != $was_sharing) {
    $seeder->setup();
  }
}

 $users = get_all_users();
 $canupload = (isset($_POST['canupload'])) ? $_POST['canupload'] : array();
 $canadmin = (isset($_POST['canadmin'])) ? $_POST['canadmin'] : array();
 $oldcanupload = (isset($_POST['oldcanupload'])) ? $_POST['oldcanupload'] : array();
 $oldcanadmin = (isset($_POST['oldcanadmin'])) ? $_POST['oldcanadmin'] : array();

 foreach($users as $username => $user) {
   $changed = false;
   if (!isset($oldcanadmin[$username]))
     $oldcanadmin[$username] = false;
   if (!isset($oldcanupload[$username]))
     $oldcanupload[$username] = false;

   if (!isset($canadmin[$username]))
     $canadmin[$username] = false;
   if (!isset($canupload[$username]))
     $canupload[$username] = false;

   if  ($oldcanadmin[$username] != $canadmin[$username])
     {
       $changed = true;
       $user['CanAdmin'] = $canadmin[$username];
     }
   if  ($oldcanupload[$username] != $canupload[$username])
     {
       $changed = true;
       $user['CanUpload'] = $canupload[$username];
     }
   update_user($username,$user['Hash'],$user['Email'],$user['CanUpload'],$user['CanAdmin']);
 }


?>
<form action="settings.php" method="POST">
<div style="border-top: 1px solid #999; margin: 2px 0 2px 0; height: 1px; font-size: 1px;">&nbsp;</div>
<p><strong>General Settings</strong></p>

<p>Website / Tracker Title:<br /><input type="textbox" name="title" size="40" value="<?php echo $settings['Title']; ?>" /></p>
<p>Site Description:<br/><textarea cols="40" rows="5" name="description" ><?php echo $settings['Description']; ?></textarea></p>

<p><input type="checkbox" name="nau" value="1" <?php echo $settings['NonAuthUpload'] ? "checked=\"checked\" ":""; ?> /> Everyone can upload.<br />
<input type="checkbox" name="aacu" value="1" <?php echo ($settings['AutoAuthCanUpload']) ? "checked=\"checked\" " : ""; ?> /> New users automatically get permission to upload.<br />
<input type="checkbox" name="hidens" value="1" <?php echo $settings['HideNonSeed'] ? "checked=\"checked\" ":""; ?> /> Hide torrents that have no seeders.<br />
<input type="checkbox" name="ping" value="1" <?php echo ($settings['Ping']) ? "checked=\"checked\" " : ""; ?> /> Periodically send the location of this tracker to BlogTorrent.com.</p>
<div style="border-top: 1px solid #999; margin: 2px 0 2px 0; height: 1px; font-size: 1px;">&nbsp;</div>
<p><strong>Server Sharing Settings</strong></p>
<p>Blog Torrent can share files from your server, as well as from your home computer.  This increases performance and avoids firewall related slowdowns at the expense of using more disk space and bandwidth on your server. You can either automatically share all files as they are uploaded or manually enable server sharing on a per-file basis. Most importantly, once your server has a full copy of the file, you don't need to continue sharing the file from a personal computer, there will always be at least one seed available.</p>
<p>Linux, Mac OS X, and UNIX servers are supported. Most Linux servers should work without entering additional settings. If you have a Mac OS X or UNIX server, you'll need to tell Blog Torrent where it can find Python. Contact your system administrator if you need help.</p>
<p><b>Remember</b>, if you turn on server sharing <b>you must have enough diskspace</b> to store each file the server is sharing <b>and enough bandwidth</b> to upload several copies of each file shared from the server. When you turn on server sharing, you can choose to start or stop sharing on any particular file.</p>
<p>Server sharing is currently <?php if (!$seeder->enabled()) echo '<strong>NOT</strong> '?>functioning</p>
<?php
   echo $seeder->setupHelpMessage();
   echo '<p>Enable server sharing: <input type="checkbox" name="sharing_enable" value="1" '.($settings['sharing_enable'] ? "checked=\"checked\" ":"")." /></p>\n";
   echo '<p>Automatically server share files: <input type="checkbox" name="sharing_auto" value="1" '.($settings['sharing_auto'] ? "checked=\"checked\" ":"")." /></p>\n";
?>
<p>Python location (OS X and UNIX servers only): <input type="textbox" name="sharing_python" value="<?php echo isset($settings['sharing_python'])?$settings['sharing_python']:''; ?>" /></p>

<p style="font-weight: bold; border-top: 1px solid #999;">Users</p>
<?php
  $users = get_all_users();
 foreach($users as $username => $user) {
   echo '<input type="hidden" name="oldcanupload['.$username.']" value="'.($user['CanUpload'] ? "1":"")."\" />\n";
   echo '<input type="hidden" name="oldcanadmin['.$username.']" value="'.($user['CanAdmin'] ? "1":"")."\" />\n";
   echo '<div style="margin-bottom: 2px;">';
   echo "<div style=\"width: 140px; font-weight: bold; float: left;\">$username</div> ";
   echo '<input type="checkbox" name="canupload['.$username.']" value="1" '.($user['CanUpload'] ? "checked=\"checked\" ":"")." /> Can upload.&nbsp;&nbsp;\n";
   echo '<input type="checkbox" name="canadmin['.$username.']" value="1" '.($user['CanAdmin'] ? "checked=\"checked\" ":"")." /> Admin&nbsp;&nbsp;\n";
   echo '<a href="settings.php?delete='.htmlspecialchars($username).'">delete</a>';
   echo '<div style="clear: both; height: 1px; font-size: 1px;">&nbsp;</div>';
   echo '</div>';
 }

?>
<div style="border-top: 1px solid #999; margin: 2px 0 2px 0; height: 1px; font-size: 1px;">&nbsp;</div>
<p><strong>Optional MySQL settings</strong></p>
<p>Blog Torrent can use a MySQL database for increased performance. If you have such a database on your webserver, please enter the information for it below to activate MySQL support</p>
<p>Currently, this installation of Blog Torrent is <?php if ($store->type()!='MySQL') echo '<strong>NOT</strong> '?>using MySQL</p>
<p>Host name: <input type="textbox" name="mysql_host" value="<?php echo isset($settings['mysql_host'])?$settings['mysql_host']:'localhost'; ?>" /></p>
<p>Database: <input type="textbox" name="mysql_database" value="<?php echo isset($settings['mysql_database'])?$settings['mysql_database']:''; ?>" /></p>
<p>Username: <input type="textbox" name="mysql_username" value="<?php echo isset($settings['mysql_username'])?$settings['mysql_username']:''; ?>" /></p>
<p>Password: <input type="textbox" name="mysql_password" value="<?php echo isset($settings['mysql_password'])?$settings['mysql_password']:''; ?>" /></p>
<input type="submit" value="Save All Changes" />

</form>
<p><a href="index.php">Return to the tracker</a></p>
</div>
</BODY>
</HTML>
<?php
}