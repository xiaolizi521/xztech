<?php
echo '				<table cellpadding="2" cellspacing="0" class="main" style="width: 100%;">
					<tr>
						<td style="width: 130px; text-align: left;" class="secondary">', $member_username, '</td>
						<td>
							<table cellpadding="0" cellspacing="0" class="secondary" style="width: 100%;">
								<tr>
									<td style="text-align: left;">', $pm_date, '</td>
									<td style="text-align: right;">', $pm_options, '</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; text-align: left; width: 130px;">
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
							', $member_joindate, '<br />
							', $member_posts, '<br />
							', $member_num, '<br />
							', $member_online, '</td>
						<td style="vertical-align: top; text-align: left;">
							<table cellpadding="3" cellspacing="0" class="main" style="width: 100%">
								<tr>
									<td style="border-bottom: 1px solid #666666">
										', $pm_subject, '</td>
								</tr>
								<tr>
									<td style="text-align: justify">', $pm_message, '</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table cellpadding="5" cellspacing="0" style="width: 100%;">
					<tr>
						<td style="height: 17px;"></td>
					</tr>
				</table>
';
?>