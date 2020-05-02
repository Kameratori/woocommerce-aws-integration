<?php 

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/GenericEvent.php';

use AWSWooCommerce\GenericEvent;
use AWSWooCommerce\SNSEvent;
use AWSWooCommerce\SQSEvent;
use AWSWooCommerce\KinesisEvent;
use AWSWooCommerce\FirehoseEvent;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GenericEventCest
{
	public function _before(UnitTester $I)
	{
		\Mockery::globalHelpers();
	}

	public function _after(UnitTester $I)
	{
			\Mockery::close();
	}

	public function shouldPublishSNSEventWithSNSTopicTargetARN(UnitTester $I)
	{
		// given
		$class = SNSEvent::class;
		$event = 'test_event';
		$target = 'arn:aws:sns:us-east-1:123:TestTopic';
		$data = [ 'test' => 'data' ];
		$mock = mock('overload:' . $class)->makePartial();
		$mock->shouldReceive('publish')->once()->andReturn($target);

		// when
		$e = new GenericEvent($target, $event, $data);
		$res = $e->publish();

		// then
		$I->assertEquals($res, $target);
	}

	public function shouldPublishSQSEventWithSQSQueueTargetARN(UnitTester $I)
	{
		// given
		$class = SQSEvent::class;
		$event = 'test_event';
		$target = 'arn:aws:sqs:us-east-1:123:TestQueue';
		$data = [ 'test' => 'data' ];
		$mock = mock('overload:' . $class)->makePartial();
		$mock->shouldReceive('publish')->once()->andReturn($target);

		// when
		$e = new GenericEvent($target, $event, $data);
		$res = $e->publish();

		// then
		$I->assertEquals($res, $target);
	}

	public function shouldPublishKinesisEventWithKinesisStreamTargetARN(UnitTester $I)
	{
		// given
		$class = KinesisEvent::class;
		$event = 'test_event';
		$target = 'arn:aws:kinesis:us-east-1:123:stream/TestStream';
		$data = [ 'test' => 'data' ];
		$mock = mock('overload:' . $class)->makePartial();
		$mock->shouldReceive('publish')->once()->andReturn($target);

		// when
		$e = new GenericEvent($target, $event, $data);
		$res = $e->publish();

		// then
		$I->assertEquals($res, $target);
	}

	public function shouldPublishFirehoseEventWithFirehoseDeliveryStreamTargetARN(UnitTester $I)
	{
		// given
		$class = FirehoseEvent::class;
		$event = 'test_event';
		$target = 'arn:aws:firehose:us-east-1:123:deliverystream/TestStream';
		$data = [ 'test' => 'data' ];
		$mock = mock('overload:' . $class)->makePartial();
		$mock->shouldReceive('publish')->once()->andReturn($target);

		// when
		$e = new GenericEvent($target, $event, $data);
		$res = $e->publish();

		// then
		$I->assertEquals($res, $target);
	}
}
