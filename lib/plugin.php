<?php namespace AwsSnsWooCommerce;

class Plugin {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		if ( class_exists( '\WC_Integration' ) ) {
			include_once 'settings.php';
			include_once 'hooks.php';

			$settings = Settings::instance();
			$hooks    = Hooks::instance();

			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			do_action( 'aws_sns_woocommerce_initialized', $hooks, $settings );
		}
	}

	public function add_integration( $integrations ) {
		$integrations[] = '\AwsSnsWooCommerce\Settings';
		return $integrations;
	}
}

new Plugin( __FILE__ );
