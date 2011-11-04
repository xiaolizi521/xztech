<?php
if ( isset ( $user_info['user_id'] ) ) {
	require_once ( './member/usercp_menu.php' );
}
else {
?>
			<form  action="<?php echo '?page=member/login' ?>" method="post">
				<div id="login_bar" class="pos">
					<label for="username"><img id="i19_2" src="./images/index_19_2.jpg" alt="Username" /></label>
					<input type="text" id="username" name="username" size="8" title="Username" />
					<label for="password"><img id="i21_2" src="./images/index_21_2.jpg" alt="Password" /></label>
					<input type="password" id="password" name="password" size="8" title="Password" />
					<input type="submit" id="submit" name="login_submit" title="Submit" value="" />
					<input type="hidden" name="cookieuser" value="1" />
				</div>
			</form>
<?php
}
?>