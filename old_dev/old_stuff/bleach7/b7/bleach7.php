<?php 
include ( "member/db.php" );
echo 'heloooooooooooooooooo';
$result_news = mysql_query ( "SELECT * FROM news ORDER BY id DESC" );
print_r($result_news);
$query=mysql_query("SELECT id, headline, poster, news from news LIMIT 0,8 ORDER BY id DESC");


while($item=mysql_fetch_array($query))
 {
 print_r($query);
  $id=$query['id'];
  $title=strip_tags($query['headline']);
  $body=strip_tags($query['news']);
  $body=substr($query['news'],0,150));
  $pubDate=strftime("%a, %d %b %Y %T %Z",$query['id']);

  // output to client
	
?>

  <item>
  <title><?print htmlentities($title,'ENT_QUOTES');?></title>
  <description><?print htmlentities($body,'ENT_QUOTES');?></description>
  <link>http://bleach7.com/index.php?page=member/comments&id=<?print $id;?>&pg=1=</link> 
  <pubDate><?print $pubDate;?></pubDate>
  </item>

<? 
  } 
?>
?>

