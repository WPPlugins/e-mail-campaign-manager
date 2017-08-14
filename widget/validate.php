<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	// input data validation
	$data = array_map('strip_tags', $_POST);
	$data = array_map('trim', $data);

	$securityHash_1 = md5(date("Y-m-d H").wp_salt("nonce"));
	$securityHash_2 = md5((date("H") == "00") ? (date('Y-m-d', strtotime("-1 days"))." 23") : (date("Y-m-d ").sprintf("%02s", intval(date("H"))-1)).wp_salt("nonce"));


	// Bye bye robots
	if (
		(!isset($data["ecm_captcha"]) || !isset($data["ecm_securityHash"]))
		|| (isset($data["ecm_captcha"]) && !empty($data["ecm_captcha"]))
		|| !((isset($data["ecm_securityHash"]) && $securityHash_1 == $data["ecm_securityHash"])
			|| (isset($data["ecm_securityHash"]) && $securityHash_2 == $data["ecm_securityHash"]))
		) {

		header("Location: https://www.google.com/");
	}


	// validate name
	if (!isset($data["ecm_name"]) || empty($data["ecm_name"])) {
		exit('<p>Please enter Your name. <strong><a href="javascript:history.back(-1);">Go back</a></strong>.</p>');
	}

	// validate e-mail
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $data["ecm_email"])) {
		exit('<p>Invalid e-mail address. <strong><a href="javascript:history.back(-1);">Go back</a></strong>.</p>');
	}


	// store in database

	// checking whether exists
	$user = $wpdb->get_row("SELECT userID FROM r1_userlist WHERE email='$data[ecm_email]' AND campaignID='$data[ecm_campaignID]' LIMIT 1", ARRAY_A);

	if (count($user) == 0) {

		$hash = md5(uniqid(rand(), true));

		$wpdb->insert(
			'r1_userlist',
			array(
				'email' => $data["ecm_email"],
				'name' => $data["ecm_name"],
				'active' => 0,
				'campaignID' => $data["ecm_campaignID"],
				'securityHash' => $hash
			),
			array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%s'
			)
		);


		$userID = $wpdb->insert_id;


		// get campaign info
		$campaign = $wpdb->get_row("SELECT campaignID, campaignName, campaignRedirect, emailTitle, emailContent
									FROM r1_campaignlist WHERE campaignID='$data[ecm_campaignID]' LIMIT 1", ARRAY_A);

		if (null !== $campaign) {
			$link = get_page_by_path("e-mail-campaign-activation")->guid.'?ecm_c='.$data["ecm_campaignID"].'&ecm_u='.$userID.'&ecm_s='.$hash;

			$search  = array(
							'[name]',
							'[link]',
							'http://[link_href]',
							'https://[link_href]',
							'[link_href]'
						);
			$replace = array(
							$data["ecm_name"],
							'<strong><a href="'.$link.'" target="_blank">'.$link.'</a></strong>',
							$link,
							$link,
							$link
						);

			$message = str_replace($search, $replace, $campaign["emailContent"]);
			$headers = array('Content-Type: text/html; charset=UTF-8');

			// sending e-mail
			$sent = wp_mail($data["ecm_email"], $campaign["emailTitle"], $message, $headers);
		}

		if ($sent == true) {
			echo '<p>OK <strong>'.$data["ecm_name"].'</strong>, You\'re in!</p><p>Now check Your e-mail account, as we\'ve just sent You some good stuff.</p><p>Go back to the <strong><a href="/">main page</a></strong>.</p>';
		} else {
			echo '<p>An error occured while sending You the e-mail. Please try again later.</p><p>Go back to the <strong><a href="/">main page</a></strong>.</p>';
		}


	} else {

		echo '<p>You have already joined this product. Thank You!</p><p>Go back to the <strong><a href="/">main page</a></strong>.</p>';
	}

?>