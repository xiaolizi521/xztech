<?
require_once('includes/classes.php');

$db = new connection;
$db->connect('localhost', 'bleach7_b7', 'funwithbleach.', 'bleach7_b7');

$id = $_COOKIE['user_id'];
$pass = $_COOKIE['password'];

$user = new user($id,$pass);

if(!$user->auth())
{ echo'You are not logged in'; }
else
{
 $user->fetch();
 echo 'Welcome '.$user->info['username'].'. ';
 
 if($user->admin())
 { echo'You are an admin of this site, hooray you!'; }
 else
 { echo'You are not an admin of this site.'; }
 
}
?>