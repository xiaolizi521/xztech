<?PHP

$iid = mysql_real_escape_string($id);
$check = mysql_query("SELECT `kudos` FROM `gallery` WHERE `id` = '$iid'");
$total = mysql_fetch_array($check);

echo'Kudos: '.$total['kudos'].'<br />';

if(empty($user_info['username']))
{
 echo '<i>You must be logged in to rate this</i>';
}
else
{
 $user = mysql_real_escape_string($user_info['username']);

 $check_rating = mysql_query("SELECT `kudos` FROM `gallery_kudos` WHERE `user` = '$user' AND `imageid` = '$iid'");
 if(mysql_num_rows($check_rating) == 0)
 {
  if(isset($_POST['give_kudos']))
  {
  $kudos = $_POST['kudos'];
  $kudos = mysql_real_escape_string($kudos);
  
  $update_kudos = mysql_query('UPDATE `gallery` SET `kudos` =(`kudos` +  '.$kudos.') WHERE id='.$id.'');
  $insert_kudos = mysql_query ("INSERT INTO gallery_kudos ( imageid, user, kudos ) VALUES ( '$iid', '$user', '$kudos' )");
  
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/?'.$_SERVER['QUERY_STRING'].'');
  }
  ?>
  <form method="post" action="">
   <select name="kudos" class="form">
    <option value="1">1 Kudos</option>
    <option value="2">2 Kudos</option>
    <option value="3">3 Kudos</option>
   </select>
   <input type="submit" value="Give" name="give_kudos" class="form" />
  </form>
  <?PHP
 }
 else
 {
 $show_kudos = mysql_fetch_array($check_rating);
 echo '<i>You gave this '.$show_kudos['kudos'].' kudos</i>';
 }
 
}

?>
