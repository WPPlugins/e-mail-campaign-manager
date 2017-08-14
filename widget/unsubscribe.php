<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

	// input data validation
	// var_dump($_GET);

	$data = array_map('strip_tags', $_GET);
	$data = array_map('trim', $data);

	// Updating database
	$updated = $wpdb->delete(
		'r1_userlist',
		array(
			'campaignID' => intval($data["ecm_c"]),
			'email' => $data["ecm_e"]
		),
		array(
			'%d',
			'%s'
		)
	);


	if (!false == $updated) {
		echo '<p>You have been successfully unsubscribed from this list.</p>';
	} else {
		header("Location: /");
	}

	exit;
?>