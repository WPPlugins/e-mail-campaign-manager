<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	////////////////////////////////////////////////////////////////////////
	// DB Object

	global $wpdb;


	////////////////////////////////////////////////////////////////////////
	// Checking whether databases exists
	// First run

	$userlist = $wpdb->get_results("SHOW TABLES LIKE 'r1_userlist'", ARRAY_A);
	if (count($userlist) == 0) {
		$wpdb->get_results("CREATE TABLE `r1_userlist` ( `userID` INT NOT NULL AUTO_INCREMENT , `email` VARCHAR(50) NOT NULL , `name` VARCHAR(30) NOT NULL , `active` INT(1) NOT NULL , `campaignID` INT NOT NULL , `dateJoined` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `securityHash` VARCHAR(32) NOT NULL , PRIMARY KEY (`userID`)) ENGINE = InnoDB DEFAULT CHARSET=utf8");
	}

	$campaignlist = $wpdb->get_results("SHOW TABLES LIKE 'r1_campaignlist'", ARRAY_A);
	if (count($campaignlist) == 0) {
		$wpdb->get_results("CREATE TABLE `r1_campaignlist` ( `campaignID` INT NOT NULL AUTO_INCREMENT , `campaignName` VARCHAR(100) NOT NULL , `campaignRedirect` VARCHAR(300) NOT NULL , `emailTitle` VARCHAR(300) NOT NULL , `emailContent` TEXT NOT NULL , `dateAdded` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`campaignID`)) ENGINE = InnoDB DEFAULT CHARSET=utf8");

		$wpdb->insert(
			'r1_campaignlist',
			array(
				'campaignName' => 'General',
				'campaignRedirect' => '',
				'emailTitle' => 'Activate Your subscription',
				'emailContent' => '
					<p>Hi there, [name]!</p>
					<p>Please click the link below to activate Your subscription:</p>
					<p>[link]</p>
					<br />
					<p>Kind regards</p>'
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);
	}


	// Creating E-mail Campaign Validation page

	if (null == get_page_by_path("e-mail-campaign-validation")) {

		$addPage = wp_insert_post (
			array(
				'comment_status'=>	'closed',
				'ping_status'	=>	'closed',
				'post_name'		=>	'e-mail-campaign-validation',
				'post_title'	=>	'E-mail Campaign Validation',
				'post_status'	=>	'publish',
				'post_type'		=>	'page',
				'post_content'	=>	'[email-camp-validation]'
			)
		);

		if ($addPage < 1) {
		?>
		<div id="message" class="error notice notice-error is-dismissible">
			<p>The plugin couldn't create the Validation page.</p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
		}
	}


	// Creating E-mail Campaign Activation page

	if (null == get_page_by_path("e-mail-campaign-activation")) {

		$addPage = wp_insert_post (
			array(
				'comment_status'=>	'closed',
				'ping_status'	=>	'closed',
				'post_name'		=>	'e-mail-campaign-activation',
				'post_title'	=>	'E-mail Campaign Activation',
				'post_status'	=>	'publish',
				'post_type'		=>	'page',
				'post_content'	=>	'[email-camp-activation]'
			)
		);

		if ($addPage < 1) {
		?>
		<div id="message" class="error notice notice-error is-dismissible">
			<p>The plugin couldn't create the Activation page.</p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
		}
	}


	// Creating E-mail Campaign Unsubscribe page

	if (null == get_page_by_path("e-mail-campaign-unsubscribe")) {

		$addPage = wp_insert_post (
			array(
				'comment_status'=>	'closed',
				'ping_status'	=>	'closed',
				'post_name'		=>	'e-mail-campaign-unsubscribe',
				'post_title'	=>	'E-mail Campaign Unsubscribe',
				'post_status'	=>	'publish',
				'post_type'		=>	'page',
				'post_content'	=>	'[email-camp-unsubscribe]'
			)
		);

		if ($addPage < 1) {
		?>
		<div id="message" class="error notice notice-error is-dismissible">
			<p>The plugin couldn't create the Unsubscribe page.</p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
		}
	}


	////////////////////////////////////////////////////////////////////////
	// Get users

	function ecm_get_users ($name=false, $limit=false, $campaign=false, $activeOnly=false, $current_page=false) {

		global $wpdb;

		if ($limit != false) {
			$l = " LIMIT ". $limit;
		} else {
			$l = "";
		}

		if ($limit != false && $current_page != false) {
			$l = " LIMIT ". ($current_page-1)*$limit.",".$limit;
		}

		if ($name != false) {
			$n = " AND email LIKE '%". $name."%' ";
		} else {
			$n = "";
		}

		if ($campaign != false) {
			$c = " AND r1_userlist.campaignID='". $campaign."' ";
		} else {
			$c = "";
		}


		if ($activeOnly != false) {
			$a = " AND r1_userlist.active='1' ";
		} else {
			$a = "";
		}


		$data = $wpdb->get_results("SELECT userID, email, name, active, r1_userlist.campaignID, dateJoined, campaignName
									 FROM r1_userlist, r1_campaignlist
									 WHERE r1_userlist.campaignID=r1_campaignlist.campaignID $n $c $a $l", ARRAY_A);
		if (count($data) > 0) {
			return $data;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Get user info

	function ecm_get_user_info ($id) {

		global $wpdb;
		$id = intval($id);

		$data = $wpdb->get_row("SELECT userID, email, name, active, r1_userlist.campaignID, dateJoined, campaignName
									 FROM r1_userlist, r1_campaignlist
									 WHERE r1_userlist.campaignID=r1_campaignlist.campaignID AND userID=$id LIMIT 1", ARRAY_A);
		if (count($data) > 0) {
			return $data;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Get campaigns

	function ecm_get_campaigns ($name=false, $limit=false, $current_page=false) {

		global $wpdb;

		if ($limit != false) {
			$l = " LIMIT ". $limit;
		} else {
			$l = "";
		}


		if ($limit != false && $current_page != false) {
			$l = " LIMIT ". ($current_page-1)*$limit.",".$limit;
		} else {
			$l = "";
		}


		if ($name != false) {
			$n = " AND campaignName LIKE '%". $name."%' ";
		} else {
			$n = "";
		}


		$data = $wpdb->get_results("SELECT r1_campaignlist.campaignID, r1_campaignlist.campaignID AS cID, campaignName,
									(SELECT COUNT(userID) FROM r1_userlist WHERE campaignID=cID) AS subscribers, dateAdded
									FROM r1_campaignlist $n $l", ARRAY_A);
		if (count($data) > 0) {
			return $data;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Get campaigns

	function ecm_get_campaign_info ($id) {

		global $wpdb;
		$id = intval($id);

		$data = $wpdb->get_row("SELECT r1_campaignlist.campaignID, r1_campaignlist.campaignID AS cID, campaignName, campaignRedirect,
									emailTitle, emailContent,
									(SELECT COUNT(userID) FROM r1_userlist WHERE campaignID=cID) AS subscribers, dateAdded
									FROM r1_campaignlist WHERE r1_campaignlist.campaignID=$id LIMIT 1", ARRAY_A);
		if (count($data) > 0) {
			return $data;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Get list of available campaigns

	function ecm_list_campaigns () {

		global $wpdb;

		$data = $wpdb->get_results("SELECT campaignID, campaignName FROM r1_campaignlist", ARRAY_A);
		if (count($data) > 0) {
			return $data;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Validates text length

	function ecm_validateTextLength ($string, $minChars=false, $maxChars=false) {

		trim($string);
		strip_tags($string);

		// dwa parametry
		if ( $minChars != false && $maxChars != false ) {

				if ( strlen($string) >= $minChars && strlen($string) <= $maxChars ) {
					return true;
				} else {
					return false;
				}
		}

		// minChars
		if ( $minChars != false ) {

				if ( strlen($string) >= $minChars ) {
					return true;
				} else {
					return false;
				}
		}

		// maxChars
		if ( $maxChars != false ) {

				if ( strlen($string) <= $maxChars ) {
					return true;
				} else {
					return false;
				}
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Validates e-mail address

	function ecm_validateEmail ($string) {

		if ( preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $string) ) {
			return true;
		}

		return false;
	}


	////////////////////////////////////////////////////////////////////////
	// Validates URL

	function ecm_validateUrl ($string) {

		if ( preg_match("/(https?:\/\/(www\.)?)[-a-z0-9+&@#\/%?=~_|!:,.;]*[\.][-a-z0-9+&@#\/%=~_|]/i", $string) ) {
			return true;
		}

		return false;
	}



	////////////////////////////////////////////////////////////////////////
	// Returns $_POST values if exists

	function ecm_post ($post) {

		if (isset($_POST[$post])) {
			return $_POST[$post];
		} else {
			return "";
		}
	}


	///////////////////////////////////////////////////////////////////////////
	// Returns $_POST values if exists or the other parameter

	function ecm_postOrData ($post, $data) {

		if (isset($_POST[$post])) {
			return $_POST[$post];
		} else {
			return $data;
		}
	}


	///////////////////////////////////////////////////////////////////////////
	// Pagination

	function ecm_getPagination ($page=1, $total=1, $onPage=20, $link="") {

		$html = '
				<span class="displaying-num">'.$total.' elements</span>
				<span class="pagination-links">
					<a class="next-page" href="'.str_replace("%n%",1,$link).'"><span class="screen-reader-text">First page</span><span aria-hidden="true">&laquo;</span></a>
			';

		// poprzednia
		if ( $page > 1 ) {
			$html .= '<a class="next-page" href="'.str_replace("%n%",$page-1,$link).'">
						<span class="screen-reader-text">Prev page</span><span aria-hidden="true">‹</span>
					</a>';
		} else {
			$html .= '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
		}

		// stronicowanie
		$pages = ceil($total/$onPage);


		// jeśli jedna strona - nie pokazujemy
		if ($pages == 1) {

			return "";

		} else {
			$html .= '
					<span class="screen-reader-text">Current page</span>
					<span id="table-paging" class="paging-input">'.$page.' of <span class="total-pages">'.$pages.'</span></span>
				';
		}


		// następna
		if ( $page * $onPage < $total ) {
			$html .= '<a class="next-page" href="'.str_replace("%n%",$page+1,$link).'">
						<span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
					</a>';
		} else {
			$html .= '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
		}


		$html .= '<a class="next-page" href="'.str_replace("%n%",$pages,$link).'"><span class="screen-reader-text">Last page</span><span aria-hidden="true">&raquo;</span></a>
				</span>';

		return $html;
	}


?>