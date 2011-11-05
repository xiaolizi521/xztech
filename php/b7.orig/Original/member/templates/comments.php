<?php
echo '
<table style="width: 100%;" cellpadding="2" cellspacing="0" class="main">
    <tr>
        <td style="width: 130px; text-align: left;" class="secondary">', $member_username, '</td>
        <td>
            <table style="width: 100%;" cellpadding="0" cellspacing="0" class="secondary">
                <tr>
                    <td style="text-align: left;">', $comment_date, '</td>
                    <td style="text-align: right;">', $comment_postnum, '</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="width: 130px; text-align: left; vertical-align: top;">
            <table style="height: 5px;">
                <tr>
                    <td></td>
                </tr>
            </table>
', $member_avatar, '
            <table style="height: 5px;">
                <tr>
                    <td></td>
                </tr>
            </table>
', $member_type, '<br />
', $member_rank, '<br />
',$member_joindate, '<br />
',$member_posts, '<br />
',$member_num, '<br />
',$member_online, '
        </td>
        <td align="right" valign="top">
            <table style="width: 100%;" cellpadding="3" cellspacing="0" class="main">
                <tr>
                    <td style="text-align: right;">';//<td style="text-align: right;">', $comment_options, '

					if(isset($comment_options))
					{
					 echo $comment_options;
					}
					
					echo'
                    <hr /></td>
                </tr>
                <tr>
                    <td style="text-align: justify">', $comment, '</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table style="width: 100%;" cellpadding="5" cellspacing="0">
    <tr>
        <td style="height: 17px;"></td>
    </tr>
</table>
';
?>
