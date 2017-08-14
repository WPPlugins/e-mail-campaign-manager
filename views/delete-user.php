<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	if (isset($_GET["action"]) && $_GET["action"]=="delete" && true==wp_verify_nonce($_GET['ecm_nonce'], 'ecm_delete_user_'.intval($_GET["userID"]))) {

		global $wpdb;
		$wpdb->delete('r1_userlist',
					array('userID' => intval($_GET["userID"])),
					array('%d'));


		?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>User deleted. Go back to <a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>">Dashboard</a></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
	}


	if (isset($_GET["userID"]) && !empty($_GET["userID"]) && !isset($_GET["action"])):

		$userID = intval($_GET["userID"]);
		$data = ecm_get_user_info($userID);
		$path = ECM_CURRENT_PATH."&tab=".ECM_CURRENT_TAB."&userID=".$userID;
		$url_with_nonce = wp_nonce_url($path, 'ecm_delete_user_'.$userID, 'ecm_nonce');

		if ($data):
?>
<h1>Are You sure to delete user: <strong><?php echo $data["name"]; ?></strong> (<?php echo $data["email"]; ?>)?</h1>
<br />
<a href="<?php echo $url_with_nonce."&action=delete"; ?>" class="button button-primary">Yes</a>
<a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>" class="button">No</a>

<?php
		else:
			echo '<p>User with this ID doesn\'t exist.</p>';
		endif;
	endif;
?>