<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Gallery &gt; Submit Fan Art </b><br />
<br />
</font><font face="Verdana" size="2"><b>Submit Fan Art</b></font><font face="Verdana" size="1"><br />
<br />
Before submitting your art make sure the file is below 800kb's, is in gif, png, or jpg format. If the work is not yours, be sure to credit the original artist in your description. 
<br />Abusing the the submission's and submitting illicit material will result in a ban from further submissions.
</em></b>
<br />
<br />

<?PHP
##############################
#  Bleach7 Gallery           #
#      By ExiledVip3r        #
##############################
if ( !isset ( $_COOKIE['user_id'] ) && !isset ( $_COOKIE['password'] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
}

$file_title = 'Submitting Fan Art';

 if(isset($_POST['submit_file']) && !empty($_POST['submit_file']))
 {
  $stamp = time();
  $stamp2 = $stamp.".";
  $ext = findexts($_FILES['uploaded']['name']); 
  $target = "media/gallery/images/";
  $target = $target . $stamp2.$ext;
  $ok = 1;
  
  //Check filetype
  if ($ext=="gif" || $ext=="jpg" || $ext=="png" || $ext=="jpeg" ) 
  { $ok = 1; }
  else 
  { $ok = 0; echo "<center><b>ERROR:</b><br />You may only upload GIF, JPG/JPEG, or PNG files.</center><br />"; }
  
  //check if all are set
  if( !isset($_POST['title']) || empty($_POST['title']) || !isset($_POST['comment']) || empty($_POST['comment']))
  { $ok = 0; echo "<center><b>ERROR:</b><br />One or more of the fields are empty.</center><br />"; }
  
  //Check File Size
  if( $_FILES['uploaded']['size'] > 800000 )
  { $ok=0; echo "<center><b>ERROR:</b><br />File is to big, make it smaller and try again.</center><br />"; }
  
	if($ok == 1)
	{
	 if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target))
	 { 
	 $thumb_path = "media/gallery/thumbs/";
	 $srcname = $stamp2.$ext;
	 $source = 'media/gallery/images/';
	 $thumb = $thumb_path.$srcname;
	 $thumb_size = 140;
	 //Create Thumbnail
	 if($ext=="jpg")
	 {thumbnailjpg($source,$thumb_path,$srcname,$thumb_size);}
	 elseif($ext=="gif")
	 {thumbnailgif($source,$thumb_path,$srcname,$thumb_size);}
	 elseif($ext=="png")
	 {thumbnailpng($source,$thumb_path,$srcname,$thumb_size);}
	 
	 
	 $location = mysql_real_escape_string($target);
	 $thumbpath = $thumb_path.$stamp2.$ext;
	 $thumb = mysql_real_escape_string($thumbpath);
	 $title = mysql_real_escape_string(htmlspecialchars($_POST['title']));
	 $poster = mysql_real_escape_string($user_info['username']);
	 $comment = mysql_real_escape_string(htmlspecialchars($_POST['comment']));
	 
	  //INSERT AS A PIECE OF FANART
	  if($_POST['submit_file'] == 'Submit Fanart!')
	  {
	  if(!$insert_query = mysql_query("INSERT INTO `gallery` ( `id`, `category`, `location`, `thumb`, `title`, `poster`, `comment` ) VALUES ( null, '1', '$location', '$thumb', '$title', '$poster', '$comment');"))
	  {echo mysql_error(); }
	  else{
	   echo "<script>alert('Fan Art submitted and awaiting approval')</script>";
	   header('Location: http://www.bleach7.com/index.php?page=fan/fanart');
	  }
	  }
	 }
	 else
	 { echo "Sorry, there was a problem uploading your file. Please try again or contact an administrator."; }
	}
 }
if(!isbanned($user_info['user_id']))
{
?>
<fieldset>
<legend class="VerdanaSize1Main">Submit Fan Art</legend>
  <form action="?page=fan/fansubmit&type=fanart" method="post" enctype="multipart/form-data">
  <table cellpadding="2" cellspacing="0" align="center" class="main" width="100%">
   <tr><td align="left"><b>Title:</b></td></tr></tr>
   <tr><td><input name="title" type="text" name="title" class="form" value="<?PHP if(isset($_POST['title'])){ echo $_POST['title']; } ?>"></td></tr>
   <tr><td align="left"><b>Upload:</b></td></tr></tr>
   <tr><td><input name="uploaded" type="file" class="form"></td></tr>
   <tr><td><b>Comment:</b></td></tr>
   <tr><td><textarea name="comment"><?PHP if(isset($_POST['comment'])){echo $_POST['comment'];} ?></textarea></td></tr>
   <tr><td>
   <input type="submit" name="submit_file" value="Submit Fanart!" size="20" class="form"  /> <input type="button" value="Reset Fields" onclick="document.login_form.reset()" class="form" />
  </table>
  </form>
</fieldset>
<br /><br />
<?PHP
}
?>