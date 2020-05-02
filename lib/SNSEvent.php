<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

use AWSWooCommerce\IEvent;
use AWSWooCommerce\Settings;

use Aws\Sns\SnsClient; 

class SNSEvent implements IEvent {
	public $target;
	public $event;
	public $data;

	public function __construct( $target, $event, $data ) {
		$this->target = $target;
		$this->event  = $event;
		$this->data   = $data;

		// load configuration
		$this->settings = Settings::instance();

		// initialize client
		$client_opts = array(
			'version' => '2010-03-31',
			'region'  => $this->settings->get_option( 'aws_region', 'us-east-1' ),
		);
		if ( $this->settings->get_option( 'aws_access_key_id' ) && $this->settings->get_option( 'aws_secret_access_key' ) ) {
			$client_opts['credentials'] = array(
				'key'    => $this->settings->get_option( 'aws_access_key_id' ),
				'secret' => $this->settings->get_option( 'aws_secret_access_key' ),
			);
		}
		$client_opts = apply_filters( 'sns_client_opts', $client_opts );

		$this->client = new SnsClient( $client_opts );
	}

	public function publish() {
		$target = $this->target;
		$event  = $this->event;
		$data   = $this->data;

		$payload = array_merge(
			array( 'event' => $event ),
			$data,
		);

		$payload = apply_filters( 'sns_publish_event', $payload, $target, $event, $data );
		$target  = apply_filters( 'sns_publish_event_topic', $target, $event, $data );

		$publish_opts = array(
			'Message'  => wp_json_encode( $payload ),
			'TopicArn' => $target,
		);
		$publish_opts = apply_filters( 'sns_publish_opts', $publish_opts, $target, $event, $data );

		try {
			$this->client->publish( $publish_opts );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}
