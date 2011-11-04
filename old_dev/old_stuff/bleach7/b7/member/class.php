<?php
class B7_User {
	var $id;
	var $username;
	var $rank;
	var $loyalty;
	var $title;
	var $can_ban;
	var $view_admin;
	var $edit_latest;
	var $edit_info;
	var $pm_limit;
	
	// Constructors
	function B7_User ( $user_info ) {
		$this->id = $user_info['user_id'];
		$this->username = $user_info['username'];
		$this->rank = $user_info['type'];
		if ( $user_info['loyalty'] == '0' ) {
			$this->loyalty = true;
		}
		else {
			$this->loyalty = false;
		}
		switch ( $this->rank ) {
			case '1':
				//	Regular Members
				$this->title = 'Member';
				$this->can_ban = false;
				$this->view_admin = false;
				$this->edit_latest = false;
				$this->edit_info = false;
				$this->pm_limit = 50;
				break;
			case '2':
				//	Privileged Members
				$this->title = 'Privileged Member';
				$this->can_ban = false;
				$this->view_admin = false;
				$this->edit_latest = false;
				$this->edit_info = false;
				$this->pm_limit = 50;
				break;
			case '10':
				//	Moderators
				$this->title = 'Moderator';
				$this->can_ban = true;
				$this->view_admin = false;
				$this->edit_latest = false;
				$this->edit_info = false;
				$this->pm_limit = 100;
				break;
			case '20':
				//	Information Team
				$this->title = 'Info Team';
				$this->can_ban = false;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 150;
				break;
			case '21':
				//	Information Team who are also Moderators
				$this->title = 'Super Moderator';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 150;
				break;
			case '30':
				//	Manga7 Team Members
				$this->title = 'M7 Team';
				$this->can_ban = false;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = false;
				$this->pm_limit = 150;
				break;
			case '31':
				//	Manga7 Team Members who are also Moderators
				$this->title = 'M7 Team | Mod';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = false;
				$this->pm_limit = 150;
				break;
			case '80':
				//	Staff Members
				$this->title = 'Staff Member';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 150;
				break;
			case '81':
				//	Staff Members who are also Manga7 Members
				$this->title = 'Awesome Member';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 150;
				break;
			case '90':
				//	Administrators
				$this->title = 'Administrator';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 300;
				break;
			case '91':
				//	Administrators who are also Manga7 Members
				$this->title = 'Admin | M7 Team';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 300;
				break;
			case '98':
				//	Webmaster
				$this->title = 'Webmaster';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 500;
				break;
			case '99':
				//	Owner of the site
				$this->title = 'Sensei';
				$this->can_ban = true;
				$this->view_admin = true;
				$this->edit_latest = true;
				$this->edit_info = true;
				$this->pm_limit = 1000;
				break;
			default:
				$this->title = 'Member';
				$this->can_ban = false;
				$this->view_admin = false;
				$this->edit_latest = false;
				$this->edit_info = false;
				$this->pm_limit = 50;
				break;
		}
	}
	
	function getID () {
		return $this->id;
	}
	
	function getRank () {
		return $this->rank;
	}
	
	function getTitle () {
		return $this->title;
	}
	
	function getCan_Ban () {
		return $this->can_ban;
	}
	
	function getView_Admin () {
		return $this->view_admin;
	}
	
	function getEdit_Latest () {
		return $this->edit_latest;
	}
	
	function getEdit_Info () {
		return $this->edit_info;
	}
	
	function getPM_Limit () {
		return $this->pm_limit;
	}
	
	function getDisplay_Username () {
		switch ( $this->rank ) {
			case '10':
				$display = '<b>' . $this->username . '</b>';
				break;
			case '20':
			case '21':
			case '30':
			case '31':
				$display = '<span style="text-decoration: underline;">' . $this->username . '</span>';
				break;
			case '80':
			case '81':
				$display = '<span style="text-decoration: underline;"><b>' . $this->username . '</b></span>';
				break;
			case '90':
			case '91':
				$display = '<b><i>' . $this->username . '</i></b>';
				break;
			case '98':
			case '99':
				$display = '<span style="text-decoration: underline;"><b><i>' . $this->username . '</i></b></span>';
				break;
			default:
				$display = $this->username;
				break;
		}
		return $display;
	}
	
	//	Take in another B7_User variable, and figure if the main B7_User (A) can ban or edit the secondary B7_User (B)
	function comment_option ( $secondary, $ban_user, $delete_link, $edit_link ) {
		$comment_options1 = '';

		//	If the current id and the secondary id are the same, it must mean that the two are the same users
		if ( $this->id == $secondary->getID() ) {
			$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
		}

		//	Go through the bannable ranks and tell what happens to each set of banners and who they can ban
		else {
			switch ( $this->rank ) {
				case '10':
				case '21':
				case '31':
					//	For Moderators, Info Team / Mod, and M7 Team / Mod
					switch ( $secondary->getRank() ) {
						//	If the secondary is a Moderator, Staff Member or Adminstrator
						case '10':
						case '21':
						case '31':
						case '80':
						case '81':
						case '90':
						case '91':
						case '98':
						case '99':
							//	They can only edit or delete
							$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
							break;
						default:
							//	Everyone else, they can ban as well
							$comment_options1 = $delete_link . ' | ' . $ban_user. ' | ' . $edit_link .' | ';
							break;
					}
					break;
				case '80':
				case '81':
					switch ( $secondary->getRank() ) {
						//	If the secondary is a Staff Member or Adminstrator
						case '80':
						case '81':
						case '90':
						case '91':
						case '98':
						case '99':
							//	They can only edit or delete
							$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
							break;
						default:
							//	Everyone else, they can ban as well
							$comment_options1 = $delete_link . ' | ' . $ban_user. ' | ' . $edit_link .' | ';
							break;
					}
					break;
				case '90':
				case '91':
					switch ( $secondary->getRank() ) {
						//	If the secondary is an Adminstrator
						case '90':
						case '91':
						case '98':
						case '99':
							//	They can only edit or delete
							$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
							break;
						default:
							//	Everyone else, they can ban as well
							$comment_options1 = $delete_link . ' | ' . $ban_user. ' | ' . $edit_link .' | ';
							break;
					}
					break;
				case '98':
					switch ( $secondary->getRank() ) {
						//	If the secondary is a fellow Webmaster or the Owner
						case '98':
						case '99':
							//	They can only edit or delete
							$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
							break;
						default:
							//	Everyone else, they can ban as well
							$comment_options1 = $delete_link . ' | ' . $ban_user. ' | ' . $edit_link .' | ';
							break;
					}
					break;
				case '99':
					switch ( $secondary->getRank() ) {
						//	If the secondary is a fellow Owner
						case '99':
							//	They can only edit or delete
							$comment_options1 = $delete_link . ' | ' . $edit_link . ' | ';
							break;
						default:
							//	Everyone else, they can ban everyone
							$comment_options1 = $delete_link . ' | ' . $ban_user. ' | ' . $edit_link .' | ';
							break;
					}
					break;
				// for the non-Moderators
				default:
					$comment_options1 = '';
					break;
			}
		}
		return $comment_options1;
	}
	
	function getPost_rank ( $post ) {
		$post_rank = '';
		
		//	For all posts below 50 don't follow any loyalty path.
		if ( $post <= '0' ) {
			$post_rank = 'Condemned Soul';
		}
		else if ( $post > '0' && $post < '10' ) {
			$post_rank = 'Spiritless';
		}
		else if ( $post >= '10' && $post < '25' ) {
			$post_rank = 'Human';
		}
		else if ( $post >= '25' && $post < '50' ) {
			$post_rank = 'Spirit Medium';
		}
		
		//	If loyalty is set to good (true)
		else if ( $this->loyalty == true ) {
			if ( $post >= '50' && $post < '100' ) {
				$post_rank = 'Plus Soul';
			}
			if ( $post >= '100' && $post < '200' ) {
				$post_rank = 'Rukongai Soul';
			}
			if ( $post >= '200' && $post < '400' ) {
				$post_rank = 'Shinigami';
			}
			if ( $post >= '400' && $post < '650' ) {
				$post_rank = 'Seated Officer';
			}
			if ( $post >= '650' && $post < '1000' ) {
				$post_rank = 'Vice-Captain';
			}
			if ( $post >= '1000' && $post < '1500' ) {
				$post_rank = 'Captain';
			}
			if ( $post >= '1500' ) {
				$post_rank = 'Vizard';
			}
		}
		
		//	If loyalty is set to bad (false)
		else if ( $this->loyalty == false ) {
			if ( $post >= '50' && $post < '100' ) {
				$post_rank = 'Earthbound Spirit';
			}
			if ( $post >= '100' && $post < '200' ) {
				$post_rank = 'Demi-Hollow';
			}
			if ( $post >= '200' && $post < '400' ) {
				$post_rank = 'Hollow';
			}
			if ( $post >= '400' && $post < '650' ) {
				$post_rank = 'Gillian';
			}
			if ( $post >= '650' && $post < '1000' ) {
				$post_rank = 'Adjucha';
			}
			if ( $post >= '1000' && $post < '1500' ) {
				$post_rank = 'Vasto Lorde';
			}
			if ( $post >= '1500' ) {
				$post_rank = 'Arrancar';
			}
		}
		
		return $post_rank;
	}
}
?>