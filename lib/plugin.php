<?php namespace AwsSnsWooCommerce;

class Plugin {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		if ( class_exists( '\WC_Integration' ) ) {
			include_once 'settings.php';
			include_once 'hooks.php';
			Settings::instance();
			Hooks::instance();
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		}
	}

	public function add_integration( $integrations ) {
		$integrations[] = '\AwsSnsWooCommerce\Settings';
		return $integrations;
	}
}

new Plugin( __FILE__ );
