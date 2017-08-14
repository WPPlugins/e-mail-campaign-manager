<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	if (isset($_GET["action"]) && $_GET["action"]=="delete" && true==wp_verify_nonce($_GET['ecm_nonce'], 'ecm_delete_not_activated')) {

		global $wpdb;
		$wpdb->get_results("DELETE FROM r1_userlist WHERE dateJoined <= (NOW() - INTERVAL 1 MONTH) AND active=0");

		?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>Users deleted. Go back to <a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>">Dashboard</a></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
	}


	if (!isset($_GET["action"])):
		$path = ECM_CURRENT_PATH."&tab=".ECM_CURRENT_TAB;
		$url_with_nonce = wp_nonce_url($path, 'ecm_delete_not_activated', 'ecm_nonce');
?>

	<h1>Delete non-activated users</h1>
	<p>Here You can delete users that haven't had clicked in Your confirmation link for more than 30 days.</p>
	<p>It can help You keep the database lightweight.</p>

	<br />
	<a href="<?php echo $url_with_nonce."&action=delete"; ?>" class="button button-primary">Yes, delete them</a>
	<a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>" class="button">No</a>

<?php endif; ?>