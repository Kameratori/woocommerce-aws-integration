<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

use AWSWooCommerce\IEvent;
use AWSWooCommerce\Settings;

use Aws\Sqs\SqsClient; 

class SQSEvent implements IEvent {
	public $target;
	public $event;
	public $data;
	public $timestamp;

	public function __construct( $target, $event, $data, $timestamp ) {
		$this->target    = $target;
		$this->event     = $event;
		$this->data      = $data;
		$this->timestamp = $timestamp;

		// load configuration
		$this->settings = Settings::instance();

		// initialize client
		$client_opts = array(
			'version' => '2012-11-05',
			'region'  => $this->settings->get_option( 'aws_region', 'us-east-1' ),
		);
		if ( $this->settings->get_option( 'aws_access_key_id' ) && $this->settings->get_option( 'aws_secret_access_key' ) ) {
			$client_opts['credentials'] = array(
				'key'    => $this->settings->get_option( 'aws_access_key_id' ),
				'secret' => $this->settings->get_option( 'aws_secret_access_key' ),
			);
		}
		$client_opts = apply_filters( 'sqs_client_opts', $client_opts );

		$this->client = new SqsClient( $client_opts );
	}

	public function publish() {
		$target    = $this->target;
		$event     = $this->event;
		$data      = $this->data;
		$timestamp = $this->timestamp;

		$payload = array_merge(
			array( 'event' => $event ),
			array( 'timestamp' => $timestamp ),
			$data,
		);

		$payload = apply_filters( 'sqs_publish_event', $payload, $target, $event, $data, $timestamp );
		$target  = apply_filters( 'sqs_publish_event_queue', $target, $event, $data, $timestamp );

		// arn format: arn:aws:sqs:<region>:<AccountId>:<QueueName>
		$arn_parts = explode( ':', $target );

		$get_queue_url_opts = array(
			'QueueOwnerAWSAccountId' => $arn_parts[4],
			'QueueName'              => $arn_parts[5],
		);
		$get_queue_url_opts = apply_filters( 'sqs_get_queue_url_opts', $get_queue_url_opts, $target, $event, $data, $timestamp );
		try {
			$res = $this->client->getQueueUrl( $get_queue_url_opts );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
			return;
		}
		$queue_url = $res['QueueUrl'];

		$send_message_opts = array(
			'MessageBody' => wp_json_encode( $payload ),
			'QueueUrl'    => $queue_url,
		);
		$send_message_opts = apply_filters( 'sqs_send_message_opts', $send_message_opts, $target, $event, $data, $timestamp );
		try {
			$this->client->sendMessage( $send_message_opts );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}
