<?php namespace AWSWooCommerce;

require_once 'Settings.php';

use AWSWooCommerce\Settings;

class Hooks {
	public function __construct( $publish ) {
		$this->publish = $publish;

		// load configuration
		$this->settings = Settings::instance();

		// hook into woocommerce business logic
		add_action( 'transition_post_status', array( $this, 'maybe_product_published' ), 100, 3 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ), 100, 2 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_shipped' ), 100, 2 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 100, 2 );
		add_action( 'woocommerce_order_status_rma_processing', array( $this, 'order_rma_processing'), 100, 2 );
	}

	public function maybe_product_published( $new_status, $old_status, $post ) {
		if ( $post->post_type !== 'product' ) {
			return;
		}

		if ( $new_status !== 'publish' || $old_status === 'publish' ) {
			return;
		}

		$event  = 'product_published';
		$target = $this->settings->get_option( 'arn_product_published' );
		if ( $target ) {
			$product = wc_get_product( $post->ID );
			$this->publish( $target, $event, $product->get_data() );
		}
	}

	public function order_paid( $order_id, $order ) {
		$event  = 'order_paid';
		$target = $this->settings->get_option( 'arn_order_paid' );
		if ( $target ) {
			$this->publish( $target, $event, $order->get_data() );
		}

		$event  = 'product_sold';
		$target = $this->settings->get_option( 'arn_product_sold' );
		$items  = $order->get_items( 'line_item' );
		if ( $target ) {
			foreach ( $items as $item ) {
				$product = $item->get_product();
				$data    = array_merge( $item->get_data(), $product->get_data() );
				$this->publish( $target, $event, $data );
			}
		}
	}

	public function order_shipped( $order_id, $order ) {
		$event  = 'order_shipped';
		$target = $this->settings->get_option( 'arn_order_shipped' );
		if ( $target ) {
			$this->publish( $target, $event, $order->get_data() );
		}

		$event  = 'product_shipped';
		$target = $this->settings->get_option( 'arn_product_shipped' );
		$items  = $order->get_items( 'line_item' );
		if ( $target ) {
			foreach ( $items as $item ) {
				$product = $item->get_product();
				$data    = array_merge( $item->get_data(), $product->get_data() );
				$this->publish( $target, $event, $data );
			}
		}
	}

	public function order_refunded( $order_id, $order ) {
		$event  = 'order_refunded';
		$target = $this->settings->get_option( 'arn_order_refunded' );
		if ( $target ) {
			$this->publish( $target, $event, $order->get_data() );
		}

		// loop through products in order
		$event  = 'product_refunded';
		$target = $this->settings->get_option( 'arn_product_refunded' );
		$items  = $order->get_items( 'line_item' );
		if ( $target ) {
			foreach ( $items as $item ) {
				$product = $item->get_product();
				$data    = array_merge( $item->get_data(), $product->get_data() );
				$this->publish( $target, $event, $data );
			}
		}
	}

	public function order_rma_processing( $order_id, $order ) {
		$event  = 'order_rma_processing';
		$target = $this->settings->get_option( 'arn_rma_processing' );
		if ( $target ) {
			$this->publish( $target, $event, $order->get_data() );
		}

		// loop through products in order
		$event  = 'product_rma_processing';
		$target = $this->settings->get_option( 'arn_product_refunded' );
		$items  = $order->get_items( 'line_item' );
		if ( $target ) {
			foreach ( $items as $item ) {
				$product = $item->get_product();
				$data    = array_merge( $item->get_data(), $product->get_data() );
				$this->publish( $target, $event, $data );
			}
		}
	}

	private function publish( $target, $event, $data ) {
		try {
			call_user_func( $this->publish, $target, $event, $data );
		} catch ( \Exception $e ) {
			error_log( $e );
		}
	}
}
