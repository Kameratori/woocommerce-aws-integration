<?php namespace AwsSnsWooCommerce;

class Settings extends \WC_Integration {
	public static $setting_constants = array(
		'aws_access_key_id'     => 'AWS_ACCESS_KEY_ID',
		'aws_secret_access_key' => 'AWS_SECRET_ACCESS_KEY',
		'aws_region'            => 'AWS_REGION',
		'topic_product_created' => 'TOPIC_PRODUCT_CREATED',
		'topic_order_paid'      => 'TOPIC_ORDER_PAID',
		'topic_order_shipped'   => 'TOPIC_ORDER_SHIPPED',
		'topic_order_refunded'  => 'TOPIC_ORDER_REFUNDED',
	);

	public function __construct() {
		$this->id                 = 'aws-sns-woocommerce';
		$this->method_title       = __( 'AWS SNS Integration', 'aws-sns-woocommerce' );
		$this->method_description = __( 'Set up SNS topics for WooCommerce events. Leaving a topic empty disables the event.', 'aws-sns-woocommerce' );

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'topic_product_created' => array(
				'title'             => self::$setting_constants['topic_product_created'],
				'type'              => 'text',
				'description'       => __( 'Topic to publish when a new product is created', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['topic_product_created'] ),
			),
			'topic_order_paid'      => array(
				'title'             => self::$setting_constants['topic_order_paid'],
				'type'              => 'text',
				'description'       => __( 'Topic to publish when an order is paid', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['topic_order_paid'] ),
			),
			'topic_order_shipped'   => array(
				'title'             => self::$setting_constants['topic_order_shipped'],
				'type'              => 'text',
				'description'       => __( 'Topic to publish when an order is shipped', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['topic_order_shipped'] ),
			),
			'topic_order_refunded'  => array(
				'title'             => self::$setting_constants['topic_order_refunded'],
				'type'              => 'text',
				'description'       => __( 'Topic to publish when an order is refunded', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['topic_order_refunded'] ),
			),
			'aws_access_key_id'     => array(
				'title'             => self::$setting_constants['aws_access_key_id'],
				'type'              => 'text',
				'description'       => __( 'IAM Access Key', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'AKIAVBCDEFGHIJKLMNOP',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['aws_access_key_id'] ),
			),
			'aws_secret_access_key' => array(
				'title'             => self::$setting_constants['aws_secret_access_key'],
				'type'              => 'text',
				'description'       => __( 'IAM Access Key Secret', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => '<secret key>',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['aws_secret_access_key'] ),
			),
			'aws_region'            => array(
				'title'             => self::$setting_constants['aws_region'],
				'type'              => 'text',
				'description'       => __( 'AWS region for SNS topics', 'aws-sns-woocommerce' ),
				'desc_tip'          => true,
				'placeholder'       => 'us-east-1',
				'default'           => 'us-east-1',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['aws_region'] ),
			),
		);
	}

	// apply constant overrides
	public function get_option( $key, $empty_value = null ) {
		if ( empty( $this->settings ) ) {
			$this->init_settings();
		}

		if ( ! isset( $this->settings[ $key ] ) ) {
			$form_fields            = $this->get_form_fields();
			$this->settings[ $key ] = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
		}

		if ( ! is_null( $empty_value ) && '' === $this->settings[ $key ] ) {
			$this->settings[ $key ] = $empty_value;
		}

		// apply constant overrides
		if ( isset( self::$setting_constants[ $key ] ) && defined( self::$setting_constants[ $key ] ) ) {
			$this->settings[ $key ] = constant( self::$setting_constants[ $key ] );
		}

		return $this->settings[ $key ];
	}

	// utility to set readonly if constant is defined
	private function readonly_if_defined( $constant ) {
		return defined( $constant ) ? array( 'readonly' => 'readonly' ) : array();
	}

	// make singleton
	private static $instance = null;
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new Settings();
		}
		return self::$instance;
	}
}
