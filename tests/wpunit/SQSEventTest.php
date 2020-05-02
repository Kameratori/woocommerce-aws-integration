<?php

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/SQSEvent.php';

use AWSWooCommerce\SQSEvent;
use Aws\Sqs\SqsClient; 

class SQSEventTest extends \Codeception\TestCase\WPTestCase
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

	public function testPublishToSQSQueue()
	{
		// given
		$event = 'test_event';
		$target = 'arn:aws:sqs:eu-west-1:123:TestQueue';
		$queue_url = 'http://localhost';
		$data = [ 'test' => 'data' ];
		$timestamp = '2020-05-02T17:27:33+00:00';
		$mock = spy(SqsClient::class);
		$mock->shouldReceive('getQueueUrl')
			->with([ 'QueueOwnerAWSAccountId' => '123', 'QueueName' => 'TestQueue'])
			->andReturn([ 'QueueUrl' => $queue_url ]);

		// when
		$snsEvent = new SQSEvent($target, $event, $data, $timestamp);
		$snsEvent->client = $mock;
		$snsEvent->publish();

		// then
		$mock->shouldHaveReceived('sendMessage')->with(\Mockery::on(function ($opts) use($queue_url, $data, $event, $timestamp) {
			$message = wp_json_encode(array_merge(
				[ 'event' => $event ],
				[ 'timestamp' => $timestamp ],
				$data
			));
			return $opts['QueueUrl'] === $queue_url && $opts['MessageBody'] === $message;
		}))->once();
	}
}
