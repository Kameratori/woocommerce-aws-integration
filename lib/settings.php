<?php namespace AwsSnsWooCommerce;

class Settings extends \WC_Integration {
	public function __construct() {
		global $woocommerce;

		$this->id                 = 'aws-sns-woocommerce';
		$this->method_title       = __( 'AWS SNS Topics', 'aws-sns-woocommerce' );
		$this->method_description = __( 'Set up SNS topics for WooCommerce events. Leaving a topic empty disables the event.', 'aws-sns-woocommerce' );

		$this->init_form_fields();
		$this->init_settings();

		$this->topic_product_created = $this->get_option( 'sns_topic_product_created' );
		$this->topic_order_paid      = $this->get_option( 'sns_topic_order_paid' );
		$this->topic_order_completed = $this->get_option( 'sns_topic_order_completed' );
		$this->topic_order_refunded  = $this->get_option( 'sns_topic_order_refunded' );

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'topic_product_created'  => array(
				'title'       => __( 'Product Created Topic ARN', 'aws-sns-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Topic to publish when a new product is created', 'aws-sns-woocommerce' ),
				'desc_tip'    => true,
				'placeholder' => 'arn:sns:123',
				'default'     => '',
			),
			'topic_order_created'       => array(
				'title'       => __( 'Order Created Topic ARN', 'aws-sns-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Topic to publish when an order is paid', 'aws-sns-woocommerce' ),
				'desc_tip'    => true,
				'placeholder' => 'arn:sns:123',
				'default'     => '',
			),
			'sns_topic_order_completed'    => array(
				'title'       => __( 'Order Shipped Topic ARN', 'aws-sns-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Topic to publish when an order is shipped', 'aws-sns-woocommerce' ),
				'desc_tip'    => true,
				'placeholder' => 'arn:sns:123',
				'default'     => '',
			),
			'topic_order_refunded'   => array(
				'title'       => __( 'Order Refunded Topic ARN', 'aws-sns-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Topic to publish when an order is refunded', 'aws-sns-woocommerce' ),
				'desc_tip'    => true,
				'placeholder' => 'arn:sns:123',
				'default'     => '',
			),
		);
	}
}
