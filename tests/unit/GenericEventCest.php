<?php 

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/GenericEvent.php';

use AWSWooCommerce\GenericEvent;
use AWSWooCommerce\SNSEvent;
use AWSWooCommerce\SQSEvent;

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

	public function shouldPublishSNSEventWithSNSTargetARN(UnitTester $I)
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

	public function shouldPublishSQSEventWithSQSTargetARN(UnitTester $I)
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
}
