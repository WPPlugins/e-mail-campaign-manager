<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly
?>

<h1>Generate subscription form</h1>

<?php if (!isset($_POST["submitted"])): ?>
<p>Here You can generate subscription form based on Your categories.
	<br />What You need to do is to copy the code or shortcode and paste it on Your website.</p>
<p>Yes, it's that simple! Magic will happen automatically.</p>
<?php endif; ?>

<?php
	if (isset($_POST["submitted"]) && $_POST["submitted"]==true && true==wp_verify_nonce($_POST['_wpnonce'], 'ecm_generate_form')) {

		$errors = array();
		$form = array_map('strip_tags', $_POST);
		$form = array_map('trim', $form);


		if (empty($form["campaign"]) || $form["campaign"] < 1) {
			$errors["campaign"] = "Please choose one of the campaigns given.";
		}


		// Errors

		if (empty($errors)) {
			?>
			<div id="message" class="updated notice notice-success is-dismissible">
				<p>Copy the code from one of fields the below. Or <a href="<?php echo ECM_CURRENT_PATH."&tab=forms"; ?>">generate new form</a>.</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>

			<h2>HTML code</h2>

			<xmp>
<form method="post" action="<?php echo get_page_by_path("e-mail-campaign-validation")->guid; ?>">
<?php if (!empty($form["heading"])): ?>
	<h2><?php echo $form["heading"]; ?></h2>
<?php endif; ?>
	<input type="text" name="ecm_name" placeholder="Name" required="required" />
	<br />
	<input type="email" name="ecm_email" placeholder="E-mail" required="required" />
	<br />

	<div style="display: none;">
		<input type="text" name="ecm_captcha" placeholder="Please do not fill this field." />
	</div>
	<input type="hidden" name="ecm_securityHash" value="<?php echo "<?php"; ?> echo md5(date('Y-m-d H').wp_salt('nonce')); <?php echo "?>"; ?>" />
	<input type="hidden" name="ecm_campaignID" value="<?php echo $form["campaign"]; ?>" />
	<button type="submit" class="button btn">Yes, I'm going in!</button>
<?php if (!empty($form["footer"])): ?>

	<small><?php echo $form["footer"]; ?></small>
<?php endif; ?>
</form>
			</xmp>

			<h2>OR shortcode</h2>
			<p>To paste in page or post body:</p>
			<code>[email-camp-manager campaign="<?php echo $form["campaign"]; ?>" heading="<?php echo $form["heading"]; ?>" footer="<?php echo $form["footer"]; ?>"]</code>

			<br /><br />
			<p>To paste in the theme:</p>
			<code><span><</span><span>?</span>php echo do_shortcode('[email-camp-manager campaign="<?php echo $form["campaign"]; ?>" heading="<?php echo $form["heading"]; ?>" footer="<?php echo $form["footer"]; ?>"]'); <span>?</span>></code>
			<?php

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
	<br /><label for="heading">Heading text:</label>
	<br /><input type="text" name="heading" id="heading" value="<?php echo ecm_post("heading"); ?>" />
	<?php if (isset($errors["heading"]) && !empty($errors["heading"])) { echo '<br /><small class="error">'. $errors["heading"] .'</small>'; } ?>

	<br />
	<br /><label for="footer">Footer small text:</label>
	<br /><input type="text" name="footer" id="footer" value="<?php echo ecm_post("footer"); ?>" />
	<?php if (isset($errors["footer"]) && !empty($errors["footer"])) { echo '<br /><small class="error">'. $errors["footer"] .'</small>'; } ?>

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

	<input type="hidden" name="submitted" value="true" />
	<?php wp_nonce_field('ecm_generate_form'); ?>

	<br />
	<br /><button type="submit" class="button button-primary">Generate form</button>
</form>
<?php endif; ?>