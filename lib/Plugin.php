<?php namespace AWSWooCommerce;

use AWSWooCommerce\Hooks;
use AWSWooCommerce\GenericEvent;

class Plugin {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		if ( class_exists( '\WC_Integration' ) ) {
			include_once 'Hooks.php';
			include_once 'GenericEvent.php';

			$hooks = new Hooks( array( $this, 'publish' ) );

			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			do_action( 'aws_sns_woocommerce_initialized', $hooks, $settings );
		}
	}

	public function publish( $target, $event, $data ) {
		$target = apply_filters( 'aws_publish_event_target', $target, $event, $data );
		$target = apply_filters( 'aws_publish_event_' . $event . 'target', $target, $event, $data );

		$event = apply_filters( 'aws_publish_event', $event, $target, $data );
		$event = apply_filters( 'aws_publish_event_' . $event, $event, $target, $data );

		$data = apply_filters( 'aws_publish_event_data', $data, $target, $event );
		$data = apply_filters( 'aws_publish_event_' . $event . '_data', $data, $target, $event );

		$e = new GenericEvent( $target, $event, $data );
		$e->publish();
	}

	public function add_integration( $integrations ) {
		$integrations[] = Settings::class;
		return $integrations;
	}
}

new Plugin( __FILE__ );
