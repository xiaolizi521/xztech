<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$id = mysql_real_escape_string ( $_GET['id'] );

if ( isset ( $id ) ) {
	$result = mysql_query ( 'SELECT * FROM `users` WHERE `username`=\'' . $id . '\'' );
	$member = mysql_fetch_array ( $result );
	$Member = new B7_User ( $member );
}

$file_title = 'Member Profile:split:' . $member['username'];

function DisplayInfo( $header, $content ) {
	global $sitetitle, $member;
	echo '					<tr>
						<td style="text-align: left;"><b>', $header, '</b></td>
						<td style="width: 3px;"></td>
						<td style="text-align: left;">';

	if ( empty ( $member[$content] ) ) {
		echo 'Not Available';
	}
	else {
		switch ( $content ) {
			case 'registered_on':
				echo DisplayDate( $member['registered_on'], 'd M y, h:i A', '1'  );
				break;
			case 'last_activity_time':
				echo DisplayDate( $member['last_activity_time'], 'd M y, h:i A', '1' );
				break;
			case 'timezone':
				if ( $member['dst'] == 1 ) {
					$timezone = ( $member['timezone'] + date('I'));
				}
				else if ( $member['dst'] == 0 ) {
					$timezone = $member['timezone'];
				}
				$zone = 3600 * $timezone;
				echo gmdate ( 'h:i A', time() + $zone );
				break;
			case 'last_activity_url':
				if ( ( time() - $member['last_activity_time'] ) <= 300 ) {
					echo '<span style="color: green;">Online</span>';
				}
				else {
						echo '<span style="color: red;">Offline</span>';
				}
				break;
			case 'email_address':
				echo '<a href="mailto:', $member['email_address'], '">Send an Email</a>';
				break;
			case 'website':
				if ( ereg ( 'www.|http://.', $member['website'] ) ) {
					echo '<a href="', $member['website'], '" target="_blank">Visit</a>';
				}
				else {
					echo '<a href="http://', $member['website'], '" target="_blank">Visit</a>';
				}
				break;
			case 'aim':
				$user_aim = str_replace ( ' ', '+', $member['aim'] );
				$aim_site_name = str_replace ( ' ', '+', $sitetitle );
				echo '<a href="aim:goim?screenname=', $user_aim, '&amp;message=Hi!+I+saw+you+from+', $aim_site_name, '">', $member['aim'], '</a>';
				break;
			case 'gender':
				if ( $member['gender'] == 'm' ) {
					echo 'Male';
				}
				else if ( $member['gender'] == 'f' ) {
					echo 'Female';
				}
				else {
					echo 'Not Telling';
				}
				break;
			case 'bday_month':
				if ( ( $member['bday_month'] == '-' && $member['bday_day'] == '-' ) || ( $member['bday_month'] != '-' && $member['bday_day'] == '-' ) ) {
					echo 'Not Available';
				}
				else if ( $member['bday_month'] == '-' && $member['bday_day'] != '-' ) {
					echo $member['bday_month'];
				}
				else {
					echo $member['bday_month'], ' ', $member['bday_day'];
				}
				if ( !empty ( $member['bday_year'] ) ) {
					echo ', ', $member['bday_year'];
				}
				break;
			default:
				echo $member[$content];
				break;
		} 
	}
	echo '</td>
					</tr>
';
}

if ( isset ( $id ) && !empty ( $id ) && mysql_num_rows ( $result ) > 0 && ( eregi ( '^[a-z0-9\-_\.]+$', $id ) ) ) {
	echo '<p style="width: 100%; text-align: center;"><b>Viewing Profile: </b>', $Member->getDisplay_Username(), '<br />
	<br />
	';
//check if the user is banned or not.
//if the function returns true (if a row is sent back) show the pwned picture
	/*if ( isbanned( $show_comments['user_id'] ) ) 
	{
		$member_avatar = '<img src="member/images/avatars/pwnd.jpg" alt="pwnd" style="width: 100px; height: 100px;" />
';
	}
	else {*/
		if ( empty ( $member['avatar'] ) ) {
			echo '<img src="', $site_url, '/', $script_folder, '/images/avatars/none.gif" alt="none" style="width: 100px; height: 100px;" />';
		}
		else {
			list ( $avatar_width, $avatar_height ) = getimagesize ( $member['avatar'] );
			if ( $avatar_width > 100 || $avatar_height > 100 ) {
				echo '<img src="', $member['avatar'], '" alt="', $member['username'], '" style="width: 100px; height: 100px;" />';
			}
			else {
				echo '<img src="', $member['avatar'], '" alt="', $member['username'], '" />';
			}
		}//show banned avatar end
	// }
	echo '<br />
</p>
';
?>
<table cellpadding="0" cellspacing="0" style="width: 100%;" class="VerdanaSize1Main">
	<tr>
		<td style="width: 49%;">
			<fieldset>
				<legend class="VerdanaSize1Main">Statistics</legend>
				<table cellpadding="0" cellspacing="5" style="width: 100%; text-align: center;" class="VerdanaSize1Main">
<?php 
DisplayInfo( $header='Joined', $content='registered_on' ); 
DisplayInfo( $header='Referrals', $content='referrals' );
DisplayInfo( $header='Posts', $content='posts' ); 
DisplayInfo( $header='Last Active', $content='last_activity_time' ); 
DisplayInfo( $header='Status', $content='last_activity_url' );

?>
				</table>
			</fieldset>
		</td>
		<td style="width: 5px;"></td>
		<td style="width: 49%;">
			<fieldset>
				<legend class="VerdanaSize1Main">Contact Information</legend>
				<table cellpadding="0" cellspacing="5" style="width: 100%; text-align: center;" class="VerdanaSize1Main">
<?php 
DisplayInfo( $header='Email', $content='email_address' ); 
DisplayInfo( $header='Website', $content='website' ); 
DisplayInfo( $header='MSN', $content='msn' ); 
DisplayInfo( $header='AIM', $content='aim' ); 
echo '					<tr>
						<td style="text-align: left;"><b>PM</b></td>
						<td style="width: 3px;"></td>
						<td style="text-align: left;"><a href="', $site_path, '/pm_compose&amp;to=', $member['username'], '">Send PM</a><td>
					</tr>
';
?>
				</table>
			</fieldset>
		</td>
	</tr>
</table>

<table style="height: 7px;">
	<tr>
		<td></td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" style="width: 100%;" class="VerdanaSize1Main">
	<tr>
		<td style="width: 49%;">
			<fieldset>
				<legend class="VerdanaSize1Main">Personal Information</legend>
				<table cellpadding="0" cellspacing="5" style="width: 100%; text-align: center;" class="VerdanaSize1Main">
<?php 
DisplayInfo( $header='Gender', $content='gender' ); 
DisplayInfo( $header='Birthday', $content='bday_month' ); 
DisplayInfo( $header='Location', $content='location' );
DisplayInfo( $header='User\'s Time', $content='timezone' );
?>
				</table>
			</fieldset>
		</td>
		<td style="width: 5px;"></td>
		<td style="width: 49%; vertical-align: top;">
			<fieldset>
				<legend class="VerdanaSize1Main">Additional Information</legend>
				<table cellpadding="0" cellspacing="5" style="width: 100%; height: 55px; text-align: center;" class="VerdanaSize1Main">
<?php 
if ( empty ( $member['biography'] ) ) {
	echo '					<tr>
						<td style="text-align: justify;">', $member['username'], ' has not written anything about ';
	if ( $member['gender'] == 'm' ) {
		echo 'himself';
	}
	else if ( $member['gender'] == 'f' ) {
		echo 'herself';
	}
	else {
		echo 'himself/herself';
	}
	echo '.</td>
					</tr>
';
}
else {

	$bio = stripslashes ( $member['biography'] );
	echo '					<tr>
						<td style="text-align: justify;">', $bio, '<td>
					</tr>
';
}
?>
				</table>
			</fieldset>
		</td>
	</tr>
</table>
<?php
}
else {
	echo '<p style="width: 100%; text-align: center;">There are no users in our database with that username</p>';
}
?>
