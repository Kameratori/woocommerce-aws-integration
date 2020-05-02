<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

use AWSWooCommerce\IEvent;
use AWSWooCommerce\Settings;

use Aws\Sqs\SqsClient; 
use Aws\Sns\AWSException; 

class SQSEvent implements IEvent {
	public $target;
	public $event;
	public $data;

	public function __construct( $target, $event, $data ) {
		$this->target = $target;
		$this->event  = $event;
		$this->data   = $data;

		// load configuration
		$this->settings = Settings::instance();

		// initialize SNS client
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
		$this->client = new SqsClient( $client_opts );
	}

	public function publish() {
		$target = $this->target;
		$event  = $this->event;
		$data   = $this->data;

		$payload = array(
			'event' => $event,
			'data'  => $data,
		);
		$payload = apply_filters( 'sns_publish_event', $payload, $target, $event );
		$payload = apply_filters( 'sns_publish_event_' . $event, $payload, $target, $event );

		$target = apply_filters( 'sns_publish_event_topic', $target, $event, $payload );
		$target = apply_filters( 'sns_publish_event_' . $event . '_topic', $target, $event, $payload );

		// arn format: arn:aws:sqs:<region>:<AccountId>:<QueueName>
		$arn_parts = explode( ':', $target );
		try {
			$queue_url = $this->client->getQueueUrl(array(
				'QueueOwnerAWSAccountId' => $arn_parts[4],
				'QueueName'              => $arn_parts[5],
			));
			$this->client->sendMessage(array(
				'MessageBody' => wp_json_encode( $payload ),
				'QueueUrl'    => $queue_url,
			));
		} catch ( AWSException $e ) {
			error_log( $e->getMessage() );
		}
	}
}
