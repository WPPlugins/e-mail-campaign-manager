<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly
?>

<h1>Export users</h1>

<?php
	if (isset($_POST["submitted"]) && $_POST["submitted"]==true && true==wp_verify_nonce($_POST['_wpnonce'], 'ecm_export_users')) {

		$errors = array();
		$form = array_map('strip_tags', $_POST);
		$form = array_map('trim', $form);

		$saved = true;

		$campaign = ecm_get_campaign_info($form["campaign"]);

		$data = ecm_get_users("","",$form["campaign"],true);

		if ($data):
			$n=1;
?>
		<h2>Users from campaign: <?php echo $campaign["campaignName"]; ?></h2>
		<textarea cols="100" rows="20" id="toCopy"><?php foreach ($data as $k=>$v): ?><?php if ($n>1) {  echo ", "; } echo $v["email"]; ?><?php $n++; endforeach; ?></textarea>

		<br />
		<a href="" class="button button-primary" id="copy">Copy to clipboard</a>


		<br /><br />
		<h2>Link to user unsubscribe:</h2>
		<p><code><?php echo get_home_url(); ?>/e-mail-campaign-unsubscribe/?ecm_c=<?php echo $campaign["campaignID"]; ?>&ecm_e=USER_EMAIL_ADDRESS</code></p>

		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function ($) {
				$("a#copy").on("click", function () {
					var $temp = $("<input>");
					$("body").append($temp);
					$temp.val($("#toCopy").text()).select();
					document.execCommand("copy");
					$temp.remove();

					$(this).after('<small class="copied">Copied!</small>');

					return false;
				});
			});
		</script>
<?php
		else:
			echo '<div id="message" class="error notice notice-error is-dismissible">
					<p>No users to export. <a href="'.ECM_CURRENT_PATH."&tab=export-users".'">Go back</a></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
		endif;
	}
?>

<?php if (!isset($saved) || $saved==false): ?>
<form method="post">
	<br />
	<br /><label for="campaign">Select campaign to export:</label>
	<br /><select name="campaign" id="campaign">
		<?php
			$campaigns = ecm_list_campaigns();
			if ($campaigns):
		?>
			<?php foreach ($campaigns as $k=>$v): ?>
				<option value="<?php echo $v["campaignID"]; ?>"<?php if (ecm_post("campaign") == $v["campaignID"]) echo ' selected="selected"'; ?>><?php echo '<br /><small class="error">'. $v["campaignName"] .'</small>'; ?></option>
			<?php endforeach; ?>
		<?php else: ?>
			<option value="1">General</option>
		<?php endif; ?>
	</select>

	<input type="hidden" name="submitted" value="true" />
	<?php wp_nonce_field('ecm_export_users'); ?>

	<br />
	<br /><button type="submit" class="button button-primary">Export users</button>
</form>
<?php endif; ?>