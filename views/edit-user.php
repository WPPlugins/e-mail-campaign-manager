<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly


	if (isset($_GET["userID"]) && !empty($_GET["userID"])):
		$userID = intval($_GET["userID"]);
		$data = ecm_get_user_info($userID);


		if ($data):
?>

		<h1>Edit user: <strong><?php echo $data["name"]; ?></strong></h1>

		<?php
			if (isset($_POST["submitted"]) && $_POST["submitted"]==true && true==wp_verify_nonce($_POST['_wpnonce'], 'ecm_edit_user_'.$userID)) {

				$errors = array();
				$form = array_map('strip_tags', $_POST);
				$form = array_map('trim', $form);

				if (!ecm_validateTextLength($form["name"], 1, 30)) {
					$errors["name"] = "User name should be at least 1 char long and the max limit is 30.";
				}

				if (!ecm_validateEmail($form["email"])) {
					$errors["email"] = "Please provide valid e-mail address.";
				}

				if ($form["active"]!=0 && $form["active"]!=1) {
					$errors["active"] = "Please specify whether the account should be active or not.";
				}

				if (empty($form["campaign"]) || $form["campaign"] < 1) {
					$errors["campaign"] = "Please choose one of the campaigns given.";
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
							'r1_userlist',
							array(
								'email' => $form["email"],
								'name' => $form["name"],
								'active' => $form["active"],
								'campaignID' => $form["campaign"]
							),
							array('userID' => intval($_GET["userID"])),
							array(
								'%s',
								'%s',
								'%d',
								'%d'
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
			<br /><label for="name">Name:</label>
			<br /><input type="text" name="name" id="name" value="<?php echo ecm_postOrdata("name", $data["name"]); ?>" />
			<?php if (isset($errors["name"]) && !empty($errors["name"])) { echo '<br /><small class="error">'. $errors["name"] .'</small>'; } ?>

			<br />
			<br /><label for="email">E-mail address:</label>
			<br /><input type="email" name="email" id="email" value="<?php echo ecm_postOrdata("email", $data["email"]); ?>" />
			<?php if (isset($errors["email"]) && !empty($errors["email"])) { echo '<br /><small class="error">'. $errors["email"] .'</small>'; } ?>

			<br />
			<br /><label for="active">Account activativated:</label>
			<br /><select name="active" id="active">
				<option value="0">No</option>
				<option value="1"<?php if (ecm_postOrdata("active", $data["active"]) == 1) echo ' selected="selected"'; ?>>Yes</option>
			</select>
			<?php if (isset($errors["active"]) && !empty($errors["active"])) { echo '<br /><small class="error">'. $errors["active"] .'</small>'; } ?>

			<br />
			<br /><label for="campaign">Select campaign:</label>
			<br /><select name="campaign" id="campaign">
				<?php
					$campaigns = ecm_list_campaigns();
					if ($campaigns):
				?>
					<?php foreach ($campaigns as $k=>$v): ?>
						<option value="<?php echo $v["campaignID"]; ?>"<?php if (ecm_postOrdata("campaign", $data["campaignID"]) == $v["campaignID"]) echo ' selected="selected"'; ?>><?php echo '<br /><small class="error">'. $v["campaignName"] .'</small>'; ?></option>
					<?php endforeach; ?>
				<?php else: ?>
					<option value="1">General</option>
				<?php endif; ?>
			</select>
			<?php if (isset($errors["campaign"]) && !empty($errors["campaign"])) { echo '<br /><small class="error">'. $errors["campaign"] .'</small>'; } ?>

			<input type="hidden" name="submitted" value="true" />
			<?php wp_nonce_field('ecm_edit_user_'.$userID); ?>

			<br />
			<br /><button type="submit" class="button button-primary">Save data</button>
		</form>
		<?php endif; ?>

<?php
		else:
			echo '<p>User with this ID doesn\'t exist.</p>';
		endif;
	endif;
?>