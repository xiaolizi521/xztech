<?php
echo "
<table width='100%' cellpadding='3' cellspacing='0' border='0' class='main'>
<tr><td bgcolor='$tableheadercolor'>
<b><font class='secondary'>$headline</font></b>
</td><tr>
<tr><td>
$news
<p>
</td></tr>
<tr><td align='right'>
<i>Posted by $poster on $date</i><br>
$comments
</td></tr>
</table>
";
?>