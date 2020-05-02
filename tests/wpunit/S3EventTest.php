<?php

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/S3Event.php';

use AWSWooCommerce\S3Event;
use Aws\S3\S3Client; 

class S3EventTest extends \Codeception\TestCase\WPTestCase
{
	protected $tester;
	public $mockSettings;
	
	public function setUp(): void
	{
		\Mockery::globalHelpers();
		parent::setUp();
	}

	public function tearDown(): void
	{
		\Mockery::close();
		parent::tearDown();
	}

	public function testPublishToS3Bucket()
	{
		// given
		$event = 'test_event';
		$bucket = 'TestBucket';
		$key = 'test';
		$target = 'arn:aws:s3:::' . $bucket . '/' . $key;
		$data = [ 'test' => 'data' ];
		$timestamp = '2020-05-02T17:27:33+00:00';
		$mock = spy(S3Client::class);

		// when
		$snsEvent = new S3Event($target, $event, $data, $timestamp);
		$snsEvent->client = $mock;
		$snsEvent->publish();

		// then
		$mock->shouldHaveReceived('putObject')->with(\Mockery::on(function ($opts) use($bucket, $data, $event, $timestamp) {
			$body = wp_json_encode(array_merge(
				[ 'event' => $event ],
				[ 'timestamp' => $timestamp ],
				$data,
			));
			return $opts['Bucket'] === $bucket && $opts['Body'] === $body;
		}))->once();
	}
}
