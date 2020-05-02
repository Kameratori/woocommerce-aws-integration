<?php namespace AWSWooCommerce;

require_once 'IEvent.php';

use AWSWooCommerce\IEvent;
use AWSWooCommerce\Settings;

use Aws\S3\S3Client; 

class S3Event implements IEvent {
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
			'version' => '2006-03-01',
			'region'  => $this->settings->get_option( 'aws_region', 'us-east-1' ),
		);
		if ( $this->settings->get_option( 'aws_access_key_id' ) && $this->settings->get_option( 'aws_secret_access_key' ) ) {
			$client_opts['credentials'] = array(
				'key'    => $this->settings->get_option( 'aws_access_key_id' ),
				'secret' => $this->settings->get_option( 'aws_secret_access_key' ),
			);
		}
		$client_opts = apply_filters( 's3_client_opts', $client_opts );

		$this->client = new S3Client( $client_opts );
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

		$payload = apply_filters( 's3_publish_event', $payload, $target, $event, $data, $timestamp );
		$target  = apply_filters( 's3_publish_event_bucket', $target, $event, $data, $timestamp );

		// arn format: arn:aws:s3:::<Bucket>/<Key>
		$arn_parts  = explode( ':', $target );
		$bucket_key = explode( '/', $arn_parts[5] );

		$bucket = $bucket_key[0];
		$key    = isset( $bucket_key[1] ) ? trailingslashit( $bucket_key[1] ) : '';

		$datetime = \DateTime::createFromFormat( 'Y-m-d\TH:i:s+', $timestamp );
		$hash     = substr( sha1( wp_json_encode( $payload ) ), 0, 8 );

		$key .= $event
			. '/' . $datetime->format( 'Y/m/d' ) . '/'
			. $datetime->format( 'Y-m-d-H-i-s' ) . '-' . $hash . '.json';

		$put_object_opts = array(
			'Bucket' => $bucket,
			'Key'    => $key,
			'Body'   => wp_json_encode( $payload ),
		);
		$put_object_opts = apply_filters( 's3_put_object_opts', $put_object_opts, $target, $event, $data, $timestamp );
		try {
			$this->client->putObject( $put_object_opts );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}
