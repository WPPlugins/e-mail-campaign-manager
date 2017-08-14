<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	if (isset($_GET["campaignID"]) && !empty($_GET["campaignID"])):
		$campaignID = intval($_GET["campaignID"]);
		$data = ecm_get_campaign_info($campaignID);


		if ($data):
?>

		<h1>Edit campaign: <strong><?php echo $data["campaignName"]; ?></strong></h1>

		<?php
			if (isset($_POST["submitted"]) && $_POST["submitted"]==true && true==wp_verify_nonce($_POST['_wpnonce'], 'ecm_edit_campaign_'.$campaignID)) {

				$errors = array();
				$content = $_POST["emailcontent"];
				$form = array_map('strip_tags', $_POST);
				$form = array_map('trim', $form);


				if (!ecm_validateTextLength($form["name"], 1, 100)) {
					$errors["name"] = "Campaign name should be at least 1 char long and the max limit is 100.";
				}

				if (!ecm_validateUrl($form["redirect"]) || !ecm_validateTextLength($form["redirect"], 1, 300)) {
					$errors["redirect"] = "Please provide valid URL. URL Should be at least 1 char long and max 300 chars long.";
				}

				if (!ecm_validateTextLength($form["emailtitle"], 1, 300)) {
					$errors["emailtitle"] = "E-mail title should be at least 1 char long and the max limit is 300.";
				}

				if (!ecm_validateTextLength($content, 1)) {
					$errors["emailcontent"] = "E-mail content should be at least 1 char long.";
				}


				// Errors

				if (empty($errors)) {
					?>
					<div id="message" class="updated notice notice-success is-dismissible">
						<p>Data saved. Go back to <a href="<?php echo ECM_CURRENT_PATH."&tab=dashboard"; ?>">Dashboard</a></p>
						<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
					</div>
					<?php

						$wpdb->update(
							'r1_campaignlist',
							array(
								'campaignName' => $form["name"],
								'campaignRedirect' => $form["redirect"],
								'emailTitle' => $form["emailtitle"],
								'emailContent' => stripslashes($content)
							),
							array('campaignID' => intval($_GET["campaignID"])),
							array(
								'%s',
								'%s',
								'%s',
								'%s'
							),
							array('%d')
						);

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
			<br /><label for="name">Campaign name:</label>
			<br /><input type="text" name="name" id="name" value="<?php echo ecm_postOrdata("name", $data["campaignName"]); ?>" class="large-text" />
			<?php if (isset($errors["name"]) && !empty($errors["name"])) { echo '<br /><small class="error">'. $errors["name"] .'</small>'; } ?>

			<br />
			<br /><label for="redirect">Link to the file that You want to be downloaded after activation. <br /> Or any other URL where user is going to be redirected after clicking the link in the e-mail, eg. link to a Thank You page:</label>
			<br /><input type="text" name="redirect" id="redirect" value="<?php echo ecm_postOrdata("redirect", $data["campaignRedirect"]); ?>" class="large-text" />
			<?php if (isset($errors["redirect"]) && !empty($errors["redirect"])) { echo '<br /><small class="error">'. $errors["redirect"] .'</small>'; } ?>

			<br />
			<br /><label for="emailtitle">E-mail title:</label>
			<br /><input type="text" name="emailtitle" id="emailtitle" value="<?php echo ecm_postOrdata("emailtitle", $data["emailTitle"]); ?>" class="large-text" />
			<?php if (isset($errors["emailtitle"]) && !empty($errors["emailtitle"])) { echo '<br /><small class="error">'. $errors["emailtitle"] .'</small>'; } ?>

			<br />
			<br /><label for="emailcontent">E-mail content:</label>
			<br /><br />
			<?php wp_editor(stripslashes(ecm_postOrdata("emailcontent", $data["emailContent"])), "emailcontent"); ?>
			<?php if (isset($errors["emailcontent"]) && !empty($errors["emailcontent"])) { echo '<br /><small class="error">'. $errors["emailcontent"] .'</small>'; } ?>

			<h3>Codes to use in e-mail content:</h3>
			<p><span class="code">[name]</span> - This tag will be replaced with the user name</p>
			<p><span class="code">[link]</span> - This tag will build the whole activation link itself, eg. <code><span><</span>a href="[link]">[link]<span><</span>/a></code></p>
			<p><span class="code">[link_href]</span> - This tag will return only the link href, so You may use it for Your custom made button, eg. <code><span><</span>a href="[link_href]"><span><</span>img src="Path/to/Your/Button" alt="" /><span><</span>/a></code></p>

			<br />
			<h3>Link to user unsubscribe:</h3>
			<p><code><?php echo get_home_url(); ?>/e-mail-campaign-unsubscribe/?ecm_c=<?php echo $campaignID; ?>&ecm_e=USER_EMAIL_ADDRESS</code></p>

			<input type="hidden" name="submitted" value="true" />
			<?php wp_nonce_field('ecm_edit_campaign_'.$campaignID); ?>

			<br />
			<br /><button type="submit" class="button button-primary">Save data</button>
		</form>
		<?php endif; ?>

<?php
		else:
			echo '<p>Campaign with this ID doesn\'t exist.</p>';
		endif;
	endif;
?>