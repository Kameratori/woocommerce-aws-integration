<?php
namespace AWSWooCommerce;

require_once 'wp-content/plugins/woocommerce-aws-integration/lib/Hooks.php';
require_once 'wp-content/plugins/woocommerce-aws-integration/lib/Settings.php';

use AWSWooCommerce\Hooks;
use AWSWooCommerce\Settings;

use Aws\Sns\SnsClient; 
use Codeception\Stub;
use Codeception\Stub\Expected;

class HooksTest extends \Codeception\TestCase\WPTestCase
{
	protected $tester;

	public $mockSettings;
	
	public function setUp(): void
	{
		\Mockery::globalHelpers();
		parent::setUp();

		$this->mockSettings = mock(Settings::class)->makePartial();
		Settings::$instance = $this->mockSettings;
	}

	public function tearDown(): void
	{
		\Mockery::close();
		parent::tearDown();
	}

	public function testPublishWhenProductIsPublished()
	{
		// given
		$this->mockSettings
			->shouldReceive('get_option')
			->with('arn_product_published')
			->andReturn('test_arn');

		$mock = spy(GenericEvent::class);
		$hooks = new Hooks([$mock, 'publish']);

		// when
		$post = static::factory()->post->create_and_get([
			'post_type' => 'product',
			'post_title' => '',
			'post_status' => 'draft',
		]);
		wp_transition_post_status( 'publish', $post->post_status, $post );

		// then
		$mock->shouldHaveReceived('publish')->once();
	}
	
	public function testPublishWhenOrderIsPaid()
	{
		// given
		$this->mockSettings
			->shouldReceive('get_option')
			->with('arn_order_paid')
			->andReturn('test_arn');

		$mock = spy(GenericEvent::class);
		$hooks = new Hooks([$mock, 'publish']);

		// when
		global $woocommerce;
		$order = wc_create_order(['status' => 'processing']);

		// then
		$mock->shouldHaveReceived('publish')->once();
	}

	public function testPublishWhenOrderIsShipped()
	{
		// given
		$this->mockSettings
			->shouldReceive('get_option')
			->with('arn_order_shipped')
			->andReturn('test_arn');

		$mock = spy(GenericEvent::class);
		$hooks = new Hooks([$mock, 'publish']);

		// when
		global $woocommerce;
		$order = wc_create_order(['status' => 'completed']);

		// then
		$mock->shouldHaveReceived('publish')->once();
	}

	public function testPublishWhenOrderIsRefunded()
	{
		// given
		$this->mockSettings
			->shouldReceive('get_option')
			->with('arn_order_refunded')
			->andReturn('test_arn');

		$mock = spy(GenericEvent::class);
		$hooks = new Hooks([$mock, 'publish']);

		// when
		global $woocommerce;
		$order = wc_create_order(['status' => 'refunded']);

		// then
		$mock->shouldHaveReceived('publish')->once();
	}
}
