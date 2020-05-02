<?php

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/KinesisEvent.php';

use AWSWooCommerce\KinesisEvent;
use Aws\Kinesis\KinesisClient; 

class KinesisEventTest extends \Codeception\TestCase\WPTestCase
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

	public function testPublishToKinesisDataStream()
	{
		// given
		$event = 'test_event';
		$stream = 'MyStream';
		$target = 'arn:aws:kinesis:eu-west-1:123:stream/' . $stream;
		$timestamp = '2020-05-02T17:27:33+00:00';
		$data = [ 'test' => 'data' ];
		$mock = spy(KinesisClient::class);

		// when
		$kinesisEvent = new KinesisEvent($target, $event, $data, $timestamp);
		$kinesisEvent->client = $mock;
		$kinesisEvent->publish();

		// then
		$mock->shouldHaveReceived('putRecord')->with(\Mockery::on(function ($opts) use($stream, $data, $event, $timestamp) {
			$data = wp_json_encode(array_merge(
				[ 'event' => $event ],
				[ 'timestamp' => $timestamp ],
				$data
			));
			return $opts['StreamName'] === $stream && $opts['Data'] === $data;
		}))->once();
	}
}
