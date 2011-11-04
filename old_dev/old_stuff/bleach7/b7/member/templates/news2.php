<?php
if(strstr($news, '<!') && strstr($news, '!>'))
{

$newsex1 = explode('<!', $news);
$newsex2 = explode('!>', $news);
$previewex1 = explode('preview(', $news);
$previewex2 = explode(');', $previewex1[1]);
$previewex3 = explode(' ', $previewex2[0]);
$var1 = str_replace(',','',$previewex3[0]);
$var2 = str_replace(',','',$previewex3[1]);
$var3 = str_replace(',','',$previewex3[2]);
$var4 = str_replace(',','',$previewex3[3]);


echo '
<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main">
    <tr>
        <td class="secondary"><b>'. $headline. '</b></td>
    </tr>
    <tr>
        <td>'.$newsex1[0];
		echo preview("$var1","$var2","$var3","$var4");
		echo $newsex2[1].'</td>
    </tr>
    <tr>
        <td align="right"><i>Posted by ' . $poster . ' on ', $date, '</i><br />
'. $comments. '</td>
    </tr>
</table>
';


}
else
{
echo '
<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main">
    <tr>
        <td class="secondary"><b>', $headline, '</b></td>
    </tr>
    <tr>
        <td>', $news, '</td>
    </tr>
    <tr>
        <td align="right"><i>Posted by ' . $poster . ' on ', $date, '</i><br />
', $comments, '</td>
    </tr>
</table>
';
}
?>
