<?php

	/*
	Plugin Name: E-mail Campaign Manager
	Plugin URI: https://abckodera.pl/aktualnosci/e-mail-campaign-manager/
	Description: Managing Your e-mail subscriptions made easy.

	Version: 1.8

	Author: Roman Cieciuch
	Author URI: http://r1media.pl
	*/


	add_action('admin_menu', 'ecm_add_admin_menu');
	add_action('admin_init', 'ecm_init');


	DEFINE ("ECM_PLUGIN_DIR_URL", plugin_dir_url(__FILE__));

	DEFINE ("ECM_PLUGIN_DIR", dirname(__FILE__));
	DEFINE ("ECM_PLUGIN_QS_NAME", "email_campaign_manager");
	DEFINE ("ECM_CURRENT_PATH", admin_url()."admin.php?page=".ECM_PLUGIN_QS_NAME);
	DEFINE ("ECM_PLUGIN_CSS", ECM_PLUGIN_DIR."/css/style.css");
	DEFINE ("ECM_PLUGIN_LOGIC", ECM_PLUGIN_DIR."/logic/logic.php");
	DEFINE ("ECM_PLUGIN_VALIDATION", ECM_PLUGIN_DIR."/widget/validate.php");
	DEFINE ("ECM_PLUGIN_ACTIVATION", ECM_PLUGIN_DIR."/widget/link.php");
	DEFINE ("ECM_PLUGIN_UNSUBSCRIBE", ECM_PLUGIN_DIR."/widget/unsubscribe.php");



	function ecm_add_admin_menu () {
		add_menu_page('E-mail Campaign Manager', 'E-mail Campaign Manager', 'manage_options', 'email_campaign_manager', 'ecm_mainpage');
	}


	function ecm_init () {



		$currentTab = "dashboard";
		if (isset($_GET["tab"]) && !empty($_GET["tab"])) {
			$currentTab = $_GET["tab"];
		}
		DEFINE ("ECM_CURRENT_TAB", $currentTab);


		function ecm_load_css () {
			wp_register_style('ecm_load_css', plugin_dir_url( __FILE__ )."css/style.css", false, '1' );
        	wp_enqueue_style('ecm_load_css');
		}
		add_action('admin_enqueue_scripts', 'ecm_load_css');

	}


	function ecm_mainpage () {
		include_once ECM_PLUGIN_LOGIC;
	?>

		<div class="wrap r1-campaign-manager">
			<div class="navi">
				<a href="<?php echo ECM_CURRENT_PATH; ?>"<?php if (ECM_CURRENT_TAB == "dashboard") echo ' class="active"'; ?>>Dashboard</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=campaigns"; ?>"<?php if (ECM_CURRENT_TAB == "campaigns") echo ' class="active"'; ?>>Campaigns</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=userlist"; ?>"<?php if (ECM_CURRENT_TAB == "userlist") echo ' class="active"'; ?>>Users</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=forms"; ?>"<?php if (ECM_CURRENT_TAB == "forms") echo ' class="active"'; ?>>Forms</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=export-users"; ?>"<?php if (ECM_CURRENT_TAB == "export-users") echo ' class="active"'; ?>>Export users</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=import-users"; ?>"<?php if (ECM_CURRENT_TAB == "import-users") echo ' class="active"'; ?>>Import users</a>
				<a href="<?php echo ECM_CURRENT_PATH."&tab=migration"; ?>"<?php if (ECM_CURRENT_TAB == "migration") echo ' class="active"'; ?>>Migration</a>
			</div>

			<?php
				if (file_exists(ECM_PLUGIN_DIR.'/views/'.ECM_CURRENT_TAB.'.php')) {
					include_once ECM_PLUGIN_DIR.'/views/'.ECM_CURRENT_TAB.'.php';
				}
			?>

			<br /><br /><br />
			<hr />
			<small>
				Plugin page: <a href="https://abckodera.pl/aktualnosci/e-mail-campaign-manager/">https://abckodera.pl/aktualnosci/e-mail-campaign-manager/</a>
				<br />Copyright &copy; <a href="https://r1media.pl">R1 Media</a> - 2014 - <?php echo date("Y"); ?>
			</small>
		</div>

	<?php
	}


	///////////////////////////////////////////////////////////////////////////
	// Shortcode email-camp-manager

	function ecm_shortcode ($atts) {

		$html = "";

		$html .= '<form method="post" action="'. get_page_by_path("e-mail-campaign-validation")->guid .'">';

					if (!empty($atts["heading"])) {
						$html .= '<h2>'. $atts["heading"] .'</h2>';
					}

		$html .= '	<input type="text" name="ecm_name" placeholder="Name" required="required" />
					<br />
					<input type="email" name="ecm_email" placeholder="E-mail" required="required" />
					<br />

					<div style="display: none;">
						<input type="text" name="ecm_captcha" placeholder="Please do not fill this field." />
					</div>';

		$html .= '	<input type="hidden" name="ecm_securityHash" value="'. md5(date("Y-m-d H").wp_salt("nonce")) .'" />
					<input type="hidden" name="ecm_campaignID" value="'. $atts["campaign"] .'" />
					<button type="submit" class="button btn">Yes, I\'m going in!</button>';

					if (!empty($atts["footer"])) {
						$html .= '<small>'. $atts["footer"] .'</small>';
					}

		$html .= '</form>';

		return $html;
	}
	add_shortcode ('email-camp-manager', 'ecm_shortcode');


	///////////////////////////////////////////////////////////////////////////
	// Shortcode email-camp-validation

	function ecm_shortcode_validation ($atts) {
		include_once ECM_PLUGIN_LOGIC;
		include_once ECM_PLUGIN_VALIDATION;
	}
	add_shortcode ('email-camp-validation', 'ecm_shortcode_validation');


	///////////////////////////////////////////////////////////////////////////
	// Shortcode email-camp-activation

	function ecm_shortcode_activation ($atts) {
		include_once ECM_PLUGIN_LOGIC;
		include_once ECM_PLUGIN_ACTIVATION;
	}
	add_shortcode ('email-camp-activation', 'ecm_shortcode_activation');


	///////////////////////////////////////////////////////////////////////////
	// Shortcode email-camp-unsubscribe

	function ecm_shortcode_unsubscribe ($atts) {
		include_once ECM_PLUGIN_LOGIC;
		include_once ECM_PLUGIN_UNSUBSCRIBE;
	}
	add_shortcode ('email-camp-unsubscribe', 'ecm_shortcode_unsubscribe');

?>