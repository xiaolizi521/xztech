<?php
echo "
<table width=\"60%\" cellpadding=\"2\" cellspacing=\"0\" class=\"main\">
    <tr>
        <td bgcolor=\"$tableheadercolor\" width=\"100\" align=\"left\" class=\"secondary\">$member_username</td>
        <td bgcolor=\"$tableheadercolor\">
            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"secondary\">
                <tr>
                    <td align=\"left\">$comment_date</td>
                    <td align=\"right\">$comment_postnum</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width=\"100\" align=\"left\" valign=\"top\">
            <table height=\"5\">
                <tr>
                    <td></td>
                </tr>
            </table>
$member_avatar
            <table height=\"5\">
                <tr>
                    <td></td>
                </tr>
            </table>
$member_type<br />
$member_rank<br />
$member_joindate<br />
$member_posts<br />
$member_num
$member_online
        </td>
        <td align=\"right\" valign=\"top\">
            <table width=\"100%\" cellpadding=\"3\" cellspacing=\"0\" class=\"main\">
                <tr>
                    <td align=\"right\">$comment_options
					<hr /></td>
                </tr>
                <tr>
                    <td style=\"text-align: justify\">$comment</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">
    <tr>
        <td height=\"17\"></td>
    </tr>
</table>
";
?>
