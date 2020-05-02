<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

use AWSWooCommerce\IEvent;
use AWSWooCommerce\Settings;

use Aws\Firehose\FirehoseClient; 

class FirehoseEvent implements IEvent {
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
			'version' => '2015-08-04',
			'region'  => $this->settings->get_option( 'aws_region', 'us-east-1' ),
		);
		if ( $this->settings->get_option( 'aws_access_key_id' ) && $this->settings->get_option( 'aws_secret_access_key' ) ) {
			$client_opts['credentials'] = array(
				'key'    => $this->settings->get_option( 'aws_access_key_id' ),
				'secret' => $this->settings->get_option( 'aws_secret_access_key' ),
			);
		}
		$client_opts = apply_filters( 'firehose_client_opts', $client_opts );

		$this->client = new FirehoseClient( $client_opts );
	}

	public function publish() {
		$target = $this->target;
		$event  = $this->event;
		$data   = $this->data;

		$payload = array(
			'event' => $event,
			'data'  => $data,
		);

		$payload = apply_filters( 'firehose_publish_event', $payload, $target, $event, $data );
		$target  = apply_filters( 'firehose_publish_event_topic', $target, $event, $data );

		// arn format: arn:aws:firehose:<region>:<AccountId>:deliverystream/<DeliveryStreamName>
		$arn_parts = explode( '/', $target );

		$put_record_opts = array(
			'DeliveryStreamName' => $arn_parts[1],
			'Record'             => array(
				'Data' => wp_json_encode( $payload ),
			),
		);
		$put_record_opts = apply_filters( 'firehose_put_record_opts', $put_record_opts, $target, $event, $data );
		try {
			$this->client->putRecord( $put_record_opts );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}
