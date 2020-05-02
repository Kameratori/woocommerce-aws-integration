<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

class GenericEvent implements IEvent {
	public $target;
	public $event;
	public $data;

	public function __construct( $target, $event, $data ) {
		$this->target = $target;
		$this->event  = $event;
		$this->data   = $data;
	}

	public function publish() {
		$target = $this->target;
		$event  = $this->event;
		$data   = $this->data;

		// SNS
		if ( strpos( $target, 'arn:aws:sns' ) === 0 ) {
			$event = new SNSEvent( $target, $event, $data );
			return $event->publish();
		}

		// SQS
		if ( strpos( $target, 'arn:aws:sqs' ) === 0 ) {
			$event = new SQSEvent( $target, $event, $data );
			return $event->publish();
		}
	}
}
