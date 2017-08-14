<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	if (isset($_GET["action"]) && $_GET["action"]=="delete" && true==wp_verify_nonce($_GET['ecm_nonce'], 'ecm_delete_campaign_'.intval($_GET["campaignID"]))) {

		global $wpdb;
		$wpdb->update(
			'r1_userlist',
			array(
				'campaignID' => 1
			),
			array('campaignID' => intval($_GET["campaignID"])),
			array(
				'%d'
			),
			array( '%d' )
		);

		$wpdb->delete('r1_campaignlist',
					array('campaignID' => intval($_GET["campaignID"])),
					array('%d'));


		?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>Campaign deleted. Go back to <a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>">Dashboard</a></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php
	}


	if (isset($_GET["campaignID"]) && !empty($_GET["campaignID"]) && !isset($_GET["action"])):

		$capaignID = intval($_GET["campaignID"]);
		$data = ecm_get_campaign_info($capaignID);
		$path = ECM_CURRENT_PATH."&tab=".ECM_CURRENT_TAB."&campaignID=".$capaignID;
		$url_with_nonce = wp_nonce_url($path, 'ecm_delete_campaign_'.$capaignID, 'ecm_nonce');

		if ($data):
?>
<h1>Are You sure to delete campaign: <strong><?php echo $data["campaignName"]; ?></strong>?</h1>
<p>All users from this campaign will be moved to General campaign.</p>
<br />
<a href="<?php echo $url_with_nonce."&action=delete"; ?>" class="button button-primary">Yes</a>
<a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>" class="button">No</a>

<?php
		else:
			echo '<p>Campaign with this ID doesn\'t exist.</p>';
		endif;
	endif;
?>