<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

	// input data validation
	// var_dump($_GET);

	$data = array_map('strip_tags', $_GET);
	$data = array_map('trim', $data);

	// Updating database

	$updated = $wpdb->update(
		'r1_userlist',
		array(
			'active' => 1
		),
		array(
			'campaignID' => intval($data["ecm_c"]),
			'userID' => intval($data["ecm_u"]),
			'securityHash' => $data["ecm_s"]
		),
		array(
			'%d'
		),
		array(
			'%d',
			'%d',
			'%s'
		)
	);

	// Redirecting
	$campaign = $wpdb->get_row("SELECT campaignRedirect FROM r1_campaignlist WHERE campaignID='$data[ecm_c]' LIMIT 1", ARRAY_A);


	if (null !== $campaign && !empty($campaign["campaignRedirect"]) && false !== $updated ) {
		header("Location: ".$campaign["campaignRedirect"]);

	} else {

		header("Location: /");
	}

	exit;
?>