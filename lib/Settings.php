<?php namespace AWSWooCommerce;

class Settings extends \WC_Integration {
	public static $setting_constants = array(
		'aws_access_key_id'     => 'AWS_ACCESS_KEY_ID',
		'aws_secret_access_key' => 'AWS_SECRET_ACCESS_KEY',
		'aws_region'            => 'AWS_REGION',
		'arn_order_paid'        => 'ARN_ORDER_PAID',
		'arn_order_shipped'     => 'ARN_ORDER_SHIPPED',
		'arn_order_refunded'    => 'ARN_ORDER_REFUNDED',
		'arn_order_rma_processing'  => 'ARN_ORDER_RMA_PROCESSING',
		'arn_product_published' => 'ARN_PRODUCT_PUBLISHED',
		'arn_product_sold'      => 'ARN_PRODUCT_SOLD',
		'arn_product_shipped'   => 'ARN_PRODUCT_SHIPPED',
		'arn_product_refunded'  => 'ARN_PRODUCT_REFUNDED',
		'arn_product_rma_processing'  => 'ARN_PRODUCT_RMA_PROCESSING',
	);

	public function __construct() {
		$this->id                 = 'woocommerce-aws-integration';
		$this->method_title       = __( 'AWS Integration', 'woocommerce-aws-integration' );
		$this->method_description = __( 'Set up AWS integration for WooCommerce events. Leaving ARN empty disables the event.', 'woocommerce-aws-integration' );

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'arn_order_paid'        => array(
				'title'             => self::$setting_constants['arn_order_paid'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when an order is paid', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_order_paid'] ),
			),
			'arn_order_shipped'     => array(
				'title'             => self::$setting_constants['arn_order_shipped'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when an order is shipped', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_order_shipped'] ),
			),
			'arn_order_refunded'    => array(
				'title'             => self::$setting_constants['arn_order_refunded'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when an order is refunded', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_order_refunded'] ),
			),
			'arn_order_rma_processing'    => array(
				'title'             => self::$setting_constants['arn_order_rma_processing'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when an order rma is processing', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_order_rma_processing'] ),
			),
			'arn_product_published' => array(
				'title'             => self::$setting_constants['arn_product_published'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when a new product is published', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_product_published'] ),
			),
			'arn_product_sold'      => array(
				'title'             => self::$setting_constants['arn_product_sold'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when a product is sold', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_product_sold'] ),
			),
			'arn_product_shipped'   => array(
				'title'             => self::$setting_constants['arn_product_shipped'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when a product is shipped', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_product_shipped'] ),
			),
			'arn_product_refunded'  => array(
				'title'             => self::$setting_constants['arn_product_refunded'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when a product is refunded', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_product_refunded'] ),
			),
			'arn_product_rma_processing'    => array(
				'title'             => self::$setting_constants['arn_product_rma_processing'],
				'type'              => 'text',
				'description'       => __( 'ARN to publish when a product rma is processing', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'arn:aws:sns:us-east-1:1234:MyTopic',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['arn_product_rma_processing'] ),
			),

			'aws_access_key_id'     => array(
				'title'             => self::$setting_constants['aws_access_key_id'],
				'type'              => 'text',
				'description'       => __( 'IAM Access Key', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => 'AKIAVBCDEFGHIJKLMNOP',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['aws_access_key_id'] ),
			),
			'aws_secret_access_key' => array(
				'title'             => self::$setting_constants['aws_secret_access_key'],
				'type'              => 'password',
				'description'       => __( 'IAM Access Key Secret', 'woocommerce-aws-integration' ),
				'desc_tip'          => true,
				'placeholder'       => '<secret key>',
				'custom_attributes' => $this->readonly_if_defined( self::$setting_constants['aws_secret_access_key'] ),
			),
			'aws_region'            => array(
				'title'             => self::$setting_constants['aws_region'],
				'type'              => 'text',
				'description'       => __( 'AWS region for SNS topics', 'woocommerce-aws-integration' ),
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
	public static $instance = null;
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new Settings();
		}
		return self::$instance;
	}
}
