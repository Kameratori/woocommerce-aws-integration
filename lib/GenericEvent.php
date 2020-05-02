<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

class GenericEvent implements IEvent {
	public $target;
	public $event;
	public $data;
	public $timestamp;

	public function __construct( $target, $event, $data, $timestamp ) {
		$this->target    = $target;
		$this->event     = $event;
		$this->data      = $data;
		$this->timestamp = $timestamp;
	}

	public function publish() {
		$target    = $this->target;
		$event     = $this->event;
		$data      = $this->data;
		$timestamp = $this->timestamp;

		// SNS Topic
		if ( strpos( $target, 'arn:aws:sns' ) === 0 ) {
			$event = new SNSEvent( $target, $event, $data, $timestamp );
			return $event->publish();
		}

		// SQS Queue
		if ( strpos( $target, 'arn:aws:sqs' ) === 0 ) {
			$event = new SQSEvent( $target, $event, $data, $timestamp );
			return $event->publish();
		}

		// Kinesis data stream
		if ( strpos( $target, 'arn:aws:kinesis' ) === 0 ) {
			$event = new KinesisEvent( $target, $event, $data, $timestamp );
			return $event->publish();
		}

		// Firehose data stream
		if ( strpos( $target, 'arn:aws:firehose' ) === 0 ) {
			$event = new FirehoseEvent( $target, $event, $data, $timestamp );
			return $event->publish();
		}

		// S3 Bucket
		if ( strpos( $target, 'arn:aws:s3' ) === 0 ) {
			$event = new S3Event( $target, $event, $data, $timestamp );
			return $event->publish();
		}
	}
}
