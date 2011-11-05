<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Gallery &gt; Fan Art </b><br />
<br />
</font><font face="Verdana" size="2"><b>Fan Art</b></font><font face="Verdana" size="1"><br />
<br />
Here you can take a stroll through all the fan submitted drawings and colorings submissions. Because this contains both anime and manga art, <em>be forewarned of spoilers.</em>
<br />
<br />
<center><a href="?page=media/fansubmit"><b>Click here to submit Fan Art</b></a></center>
<br />
<?PHP
##############################
#  Bleach7 Gallery           #
#      By ExiledVip3r        #
##############################
$file_title = 'Fan Art';

if(empty($_GET['order']))
{ $order = 'DESC'; }
else
{ $order = $_GET['order']; }

// rows to return
$limit = 15; 

// 1 = fanart 2 = wallpaper
$cat = 1;

// Build SQL Query  

//User is set
if(isset($_GET['user']))
{
 if(isset($_GET['sort']) && $_GET['sort'] == 'views')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' AND `poster` = '$_GET[user]' ORDER BY `views` $order"; }
 elseif(isset($_GET['sort']) && $_GET['sort'] == 'date')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' AND `poster` = '$_GET[user]' ORDER BY `id` $order"; }
 
 else
 {$art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' AND `poster` = '$_GET[user]' ORDER BY `id` $order";}
}

//User is not set, but sort order is.
elseif(!isset($_GET['user']) && isset($_GET['sort']))
{
 if($_GET['sort'] == 'views')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' ORDER BY `views` $order"; }
 elseif($_GET['sort'] == 'date')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' ORDER BY `id` $order"; }
  elseif($_GET['sort'] == 'kudos')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' ORDER BY `kudos` $order"; }
  elseif($_GET['sort'] == 'comments')
 { $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' ORDER BY `comments` $order"; }

}

//no sort or user set default to newest to latest
else{ $art_query = "SELECT * FROM `gallery` WHERE `category` = '$cat' AND `approved` = '1' ORDER BY `id` DESC"; }

 $numresults=mysql_query($art_query);
 $numrows=mysql_num_rows($numresults);

// next determine if s has been passed to script, if not use 0
  if (empty($_GET['s'])) {
  $s = 0;
  }
  else
  $s = $_GET['s'];

// get results
  $art_query .= " limit ".$s.",$limit";
  $result = mysql_query($art_query) or die("Couldn't execute query");

  $count = 1 + $s ;

if(empty($_GET['sort']))
{ $sort = 'null'; }
else 
{ $sort = $_GET['sort']; }
?>
<div align="right"><i>
<?
//Sort by Fields
if(!isset($_GET['user']) && empty($_GET['user']) && $sort == 'null')
{
echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';
}
elseif($sort == 'date' && $order == 'DESC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=ASC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'date' && $order == 'ASC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'views' && $order == 'DESC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=ASC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'views' && $order == 'ASC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'kudos' && $order == 'DESC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=ASC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'kudos' && $order == 'ASC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
elseif($sort == 'comments' && $order == 'DESC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=ASC">Comments</a>
';}
elseif($sort == 'comments' && $order == 'ASC' && empty($_GET['user']))
{echo'
Sort by: <a href="?page=fan/fanart&sort=date&order=DESC">Date</a>, <a href="?page=fan/fanart&sort=views&order=DESC">Views</a>, <a href="?page=fan/fanart&sort=kudos&order=DESC">Kudos</a>, <a href="?page=fan/fanart&sort=comments&order=DESC">Comments</a>
';}
?>
</i></div>
<?
echo'
<center>
<table>
 <tr>';
// now you can display the results returned

if(mysql_num_rows($result)==0)
{
echo '<h4>There is currently no submitted Fan art, be the first and <a href="?page=media/fansubmit">submit</a> now!</h4>';
}
  while ($art= mysql_fetch_array($result)) {
  $title = truncate($art["title"], 50); 

  echo '
  <td>
  <table class="artg"><tr><td align="center">
  <a href="?page=fan/fanartview&id='.$art["id"].'">
   '.stripslashes($title).'<br />
   <img src="'.$art["thumb"].'" /></a><br />
   <a href="?page=member/member&id='.$art["poster"].'">Posted by: '.$art["poster"].'</a><br />
   <em>Kudos: '.$art["kudos"].'<br />
   Views: '.$art["views"].'</em>
  
  </td></tr></table>
  </td>
  ';
  if($count % 3 == 0)
  { echo '</tr><tr>'; }
  echo'
  ' ;
  $count++ ;
  }
echo'
</tr>
</table>
</center>';
$currPage = (($s/$limit) + 1);

//break before paging
  echo "<br />";

  // next we need to do the links to other results
  if ($s>=1) { // bypass PREV link if s is 0
  $prevs=($s-$limit);
  
  
  if(empty($_GET['user']) && isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$prevs&sort=$sort&order=$order\">&lt;&lt;Prev $limit</a>";
  }
  elseif(empty($_GET['user'])  && !isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$prevs\">&lt;&lt;Prev $limit &gt;&gt;</a>";
  }
  elseif(!empty($_GET['user']) && isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$prevs&user=".$_GET['user']."&sort=$sort&order=$order\">&lt;&lt;Prev $limit</a>";
  }
  elseif(!empty($_GET['user']) && !isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$prevs&user=".$_GET['user']."\">&lt;&lt;Prev $limit</a>";
  }
  }

// calculate number of pages needing links
  $pages=intval($numrows/$limit);

// $pages now contains int of pages needed unless there is a remainder from division

  if ($numrows%$limit) {
  // has remainder so add one page
  $pages++;
  }

// check to see if last page
  if (!((($s+$limit)/$limit)==$pages) && $pages!=1) {

  // not last page so give NEXT link
  $news=$s+$limit;
	
  if(empty($_GET['user']) && isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$news&sort=$sort&order=$order\">Next $limit &gt;&gt;</a>";
  }
  elseif(empty($_GET['user'])  && !isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$news\">Next $limit &gt;&gt;</a>";
  }
  elseif(!empty($_GET['user']) && isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$news&user=".$_GET['user']."&sort=$sort&order=$order\">Next $limit &gt;&gt;</a>";
  }
  elseif(!empty($_GET['user']) && !isset($_GET['sort']))
  {	
  echo "&nbsp;<a href=\"?page=fan/fanart&s=$news&user=".$_GET['user']."\">Next $limit &gt;&gt;</a>";
  }
  }

$a = $s + ($limit) ;
  if ($a > $numrows) { $a = $numrows ; }
  $b = $s + 1 ;
  echo "<p>Showing images $b to $a of $numrows</p>";
?>

<a href="http://www.bleach7.com/?page=member/gallery">Back to Gallery Selection</a><br /><br />