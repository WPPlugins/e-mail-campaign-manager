<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly
?>

<h1>Import users</h1>

<?php
	if (isset($_POST["submitted"]) && $_POST["submitted"]==true && true==wp_verify_nonce($_POST['_wpnonce'], 'ecm_import_users')) {

		$errors = array();
		$form = array_map('strip_tags', $_POST);
		$form = array_map('trim', $form);


		if (!ecm_validateTextLength($form["emails"], 5)) {
			$errors["emails"] = "Please provide at least one e-mail address.";
		}

		if (empty($form["campaign"]) || $form["campaign"] < 1) {
			$errors["campaign"] = "Please choose one of the campaigns given.";
		}


		// Errors

		if (empty($errors)) {
			?>
			<div id="message" class="updated notice notice-success is-dismissible">
				<p>Yaay, users imported!</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>
			<?php

				$emails = explode(",", $form["emails"]);

				if (count($emails) > 0) {
					foreach ($emails as $email) {
						$email = trim($email);

						if ( ecm_validateEmail($email) ) {
							$user_exists = false;
							$user_exists = $wpdb->get_row("SELECT userID FROM r1_userlist WHERE email='$email' AND campaignID='$form[campaign]' LIMIT 1", ARRAY_A);

							if (count($user_exists) == 0) {

								$wpdb->insert(
									'r1_userlist',
									array(
										'email' => $email,
										'name' => "",
										'active' => 1,
										'campaignID' => $form["campaign"],
										'securityHash' => md5(uniqid(rand(), true))
									),
									array(
										'%s',
										'%s',
										'%d',
										'%d',
										'%s'
									)
								);
							}

						} else {
							?>
							<div id="message" class="error notice notice-error is-dismissible">
								<p>E-mail address <strong><?php echo $email; ?></strong> is not a valid e-mail.</p>
								<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
							</div>
							<?php
						}
					}
				}

			$_POST = array();
			$saved = true;

		} else {
			?>
			<div id="message" class="error notice notice-error is-dismissible">
				<p>The form contains some errors. Please fix them before next submission.</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>
			<?php
		}

	}
?>

<?php if (!isset($saved) || $saved==false): ?>
<form method="post">
	<br />
	<br /><label for="campaign">Select campaign:</label>
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
	<?php if (isset($errors["campaign"]) && !empty($errors["campaign"])) { echo '<br /><small class="error">'. $errors["campaign"] .'</small>'; } ?>

	<br />
	<br /><label for="emails">Paste e-mails to add:</label>
	<br /><textarea cols="100" rows="20" name="emails"><?php echo ecm_post("emails"); ?></textarea>
	<br /><small>Comma-separated, please. Eg. aaa@bbb.cc, ddd@ee.ff</small>

	<input type="hidden" name="submitted" value="true" />
	<?php wp_nonce_field('ecm_import_users'); ?>

	<br />
	<br /><button type="submit" class="button button-primary">Import users</button>
</form>
<?php endif; ?>