<?php

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/SNSEvent.php';

use AWSWooCommerce\SNSEvent;
use Aws\Sns\SnsClient; 

class SNSEventTest extends \Codeception\TestCase\WPTestCase
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

	public function testPublishToSNSTopic()
	{
		// given
		$event = 'test_event';
		$target = 'arn:aws:sns:123:TestTopic';
		$data = [ 'test' => 'data' ];
		$timestamp = '2020-05-02T17:27:33+00:00';
		$mock = spy(SnsClient::class);

		// when
		$snsEvent = new SNSEvent($target, $event, $data, $timestamp);
		$snsEvent->client = $mock;
		$snsEvent->publish();

		// then
		$mock->shouldHaveReceived('publish')->with(\Mockery::on(function ($opts) use($target, $data, $event, $timestamp) {
			$message = wp_json_encode(array_merge(
				[ 'event' => $event ],
				[ 'timestamp' => $timestamp ],
				$data
			));
			return $opts['TopicArn'] === $target && $opts['Message'] === $message;
		}))->once();
	}
}
