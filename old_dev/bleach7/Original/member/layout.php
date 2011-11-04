<h1>Layout Test Template</h1>
<b>BANNER</b>
<p>
<b>CONTENT BELOW:</b>
<table border="1" cellpadding="2"><tr><td valign="top">
<?php
include ( "../$page.php" );
if ( $file_availability == 2 ) {
echo "This page is limited to members only. Register or Login to view this page";
} else {
echo "<p><font face='Verdana' size='2'><b><font face='Franklin Gothic Medium Cond' size='5'>$file_title</font></u></b><p>";
echo nl2br( html_entity_decode ( $file_content, ENT_QUOTES ) );
}
?>
</td></tr></table>
<p><b>FOOTER</b>







