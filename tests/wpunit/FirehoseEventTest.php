<?php

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/FirehoseEvent.php';

use AWSWooCommerce\FirehoseEvent;
use Aws\Firehose\FirehoseClient; 

class FirehoseEventTest extends \Codeception\TestCase\WPTestCase
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

	public function testPublishToFirehoseDeliveryStream()
	{
		// given
		$event = 'test_event';
		$stream = 'MyStream';
		$target = 'arn:aws:firehose:eu-west-1:123:deliverystream/' . $stream;
		$data = [ 'test' => 'data' ];
		$timestamp = '2020-05-02T17:27:33+00:00';
		$mock = spy(FirehoseClient::class);

		// when
		$firehoseEvent = new FirehoseEvent($target, $event, $data, $timestamp);
		$firehoseEvent->client = $mock;
		$firehoseEvent->publish();

		// then
		$mock->shouldHaveReceived('putRecord')->with(\Mockery::on(function ($opts) use($stream, $data, $event, $timestamp) {
			$data = wp_json_encode(array_merge(
				[ 'event' => $event ],
				[ 'timestamp' => $timestamp ],
				$data
			));
			return $opts['DeliveryStreamName'] === $stream && $opts['Record']['Data'] === $data;
		}))->once();
	}
}
