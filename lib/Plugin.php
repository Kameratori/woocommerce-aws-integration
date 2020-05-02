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
			include_once 'SNSEvent.php';
			include_once 'SQSEvent.php';
			include_once 'KinesisEvent.php';
			include_once 'FirehoseEvent.php';
			include_once 'S3Event.php';

			$hooks = new Hooks( array( $this, 'publish' ) );

			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			do_action( 'aws_sns_woocommerce_initialized', $hooks, $settings );
		}
	}

	public function publish( $target, $event, $data, $timestamp = null ) {
		$target = apply_filters( 'aws_publish_event_target', $target, $event, $data, $timestamp );
		$event  = apply_filters( 'aws_publish_event_name', $event, $target, $data, $timestamp );
		$data   = apply_filters( 'aws_publish_event_data', $data, $target, $event, $timestamp );

		$timestamp = isset( $timestamp ) ? $timestamp : gmdate( 'c' );
		$timestamp = apply_filters( 'aws_publish_event_timestamp', $timestamp, $target, $event, $data );

		$e = new GenericEvent( $target, $event, $data, $timestamp );
		$e->publish();
	}

	public function add_integration( $integrations ) {
		$integrations[] = Settings::class;
		return $integrations;
	}
}

new Plugin( __FILE__ );
