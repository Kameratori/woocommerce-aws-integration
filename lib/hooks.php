<?php namespace AwsSnsWooCommerce;

use Aws\Sns\SnsClient; 
use AwsSnsWooCommerce\Settings;

class Hooks {
	public function __construct() {
		// load configuration
		$this->settings = Settings::instance();

		// initialize SNS client
		try {
			$client_opts = array(
				'version' => '2010-03-31',
				'region'  => $this->get_setting( 'aws_region', 'us-east-1' ),
			);
			if ( $this->get_setting( 'aws_access_key_id' ) && $this->get_setting( 'aws_secret_access_key' ) ) {
				$client_opts['credentials'] = array(
					'key'    => $this->get_setting( 'aws_access_key_id' ),
					'secret' => $this->get_setting( 'aws_secret_access_key' ),
				);
			}
			$this->client = new SnsClient( $client_opts );
		} catch ( Exception $e ) {
			error_log( $e );
		}

		// hook into woocommerce business logic
		add_action( 'transition_post_status', array( $this, 'maybe_product_published' ), 100, 3 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ), 100, 2 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_shipped' ), 100, 2 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 100, 2 );
	}

	public function publish( $topic, $event, $data ) {
		$data  = apply_filters( 'sns_publish_event_' . $event . '_data', $data, $event, $topic );
		$topic = apply_filters( 'sns_publish_event_' . $event . '_topic', $topic, $event, $data );

		$this->client->publish(array(
			'Message'  => wp_json_encode(array( 
				'event' => $event,
				'data'  => $data,
			)),
			'TopicArn' => $topic,
		));
	}

	public function maybe_product_published( $new_status, $old_status, $post ) {
		if ( $post->post_type !== 'product' ) {
			return;
		}

		if ( $new_status !== 'publish' || $old_status === 'publish' ) {
			return;
		}
		
		$event = 'product_published';
		$topic = $this->get_setting( 'topic_product_published' );
		if ( $topic ) {
			try {
				$product = wc_get_product( $post->ID );
				$this->publish( $topic, $event, $product->get_data() );
			} catch ( Exception $e ) {
				error_log( $e );
			}
		}
	}

	public function order_paid( $order_id, $order ) {
		$event = 'order_paid';
		$topic = $this->get_setting( 'topic_order_paid' );
		if ( $topic ) {
			try {
				$this->publish( $topic, $event, $order->get_data() );
			} catch ( Exception $e ) {
				error_log( $e );
			}
		}

		// loop through products in order
		$event = 'product_sold';
		$topic = $this->get_setting( 'topic_product_sold' );
		$items = $order->get_items( 'line_item' );
		if ( $topic ) {
			foreach ( $items as $item ) {
				try {
					$product = $item->get_product();
					$data    = array_merge( $item->get_data(), $product->get_data() );
					$this->publish( $topic, $event, $data );
				} catch ( Exception $e ) {
					error_log( $e );
				}
			}
		}
	}

	public function order_shipped( $order_id, $order ) {
		$event = 'order_shipped';
		$topic = $this->get_setting( 'topic_order_shipped' );
		if ( $topic ) {
			try {
				$this->publish( $topic, $event, $order->get_data() );
			} catch ( Exception $e ) {
				error_log( $e );
			}
		}

		// loop through products in order
		$event = 'product_shipped';
		$topic = $this->get_setting( 'topic_product_shipped' );
		$items = $order->get_items( 'line_item' );
		if ( $topic ) {
			foreach ( $items as $item ) {
				try {
					$product = $item->get_product();
					$data    = array_merge( $item->get_data(), $product->get_data() );
					$this->publish( $topic, $event, $data );
				} catch ( Exception $e ) {
					error_log( $e );
				}
			}
		}
	}

	public function order_refunded( $order_id, $order ) {
		$event = 'order_refunded';
		$topic = $this->get_setting( 'topic_order_refunded' );
		if ( $topic ) {
			try {
				$this->publish( $topic, $event, $order->get_data() );
			} catch ( Exception $e ) {
				error_log( $e );
			}
		}

		// loop through products in order
		$event = 'product_refunded';
		$topic = $this->get_setting( 'topic_product_refunded' );
		$items = $order->get_items( 'line_item' );
		if ( $topic ) {
			foreach ( $items as $item ) {
				try {
					$product = $item->get_product();
					$data    = array_merge( $item->get_data(), $product->get_data() );
					$this->publish( $topic, $event, $data );
				} catch ( Exception $e ) {
					error_log( $e );
				}
			}
		}
	}

	// get setting value
	private function get_setting( $key, $default = null ) {
		return $this->settings->get_option( $key, $default );
	}

	// make singleton
	private static $instance = null;
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new Hooks();
		}
		return self::$instance;
	}
}
