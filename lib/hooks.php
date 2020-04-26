<?php namespace AwsSnsWooCommerce;

class Hooks {
	public function __construct() {
		global $woocommerce;

		// load topics
		$this->topic_product_created = $this->get_option( 'sns_topic_product_created' );
		$this->topic_order_paid      = $this->get_option( 'sns_topic_order_paid' );
		$this->topic_order_completed = $this->get_option( 'sns_topic_order_completed' );
		$this->topic_order_refunded  = $this->get_option( 'sns_topic_order_refunded' );

		// hook into woocommerce business logic
		add_action( 'transition_post_status', array( $this, 'maybe_product_published' ), 100, 3 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ), 100, 2 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_completed' ), 100, 2 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 100, 2 );
	}

	public function maybe_product_published( $new_status, $old_status, $post ) {
		if ( $post->post_type !== 'product' ) {
			return;
		}

		if ( $new_status !== 'publish' ) {
			return;
		}
		
		// todo
	}

	public function order_paid( $order_id, $order ) {
		// todo
	}

	public function order_completed( $order_id, $order ) {
		// todo
	}

	public function order_refunded( $order_id, $order ) {
		// todo
	}

	private function get_option( $opt ) {
		return get_option( $opt );
	}
}

new Hooks( __FILE__ );
