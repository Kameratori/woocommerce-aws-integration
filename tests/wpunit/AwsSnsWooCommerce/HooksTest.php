<?php
namespace AwsSnsWooCommerce;

use AwsSnsWooCommerce\Hooks;

use Aws\Sns\SnsClient; 
use Codeception\Stub;
use Codeception\Stub\Expected;


class HooksTest extends \Codeception\TestCase\WPTestCase
{
		protected $tester;
		
		public function setUp(): void
		{
				parent::setUp();
				\Mockery::globalHelpers();
		}

		public function tearDown(): void
		{
				\Mockery::close();
				parent::tearDown();
		}

		public function testPublishToTopicWhenProductIsPublished()
		{
				// given
				global $test_topic;
				$test_topic = 'arn:aws:sns:123:TestTopic';
				$hooks = Hooks::instance();
				$hooks->settings->settings['topic_product_published'] = $test_topic;

				$mock = spy(SnsClient::class);
				$hooks->client = $mock;

				// when
				$post = static::factory()->post->create_and_get([
					'post_type' => 'product',
					'post_title' => '',
					'post_status' => 'draft',
				]);
				wp_transition_post_status( 'publish', $post->post_status, $post );

				// then
				$mock->shouldHaveReceived('publish')->with(\Mockery::on(function ($opts) {
					global $test_topic;
					return $opts['TopicArn'] === $test_topic;
				}));
		}
		
		public function testPublishToTopicWhenOrderStatus()
		{
				// given
				global $test_topic;
				$test_topic = 'arn:aws:sns:123:TestTopic';
				$hooks = Hooks::instance();
				$hooks->settings->settings['topic_order_paid'] = $test_topic;

				$mock = spy(SnsClient::class);
				$hooks->client = $mock;

				// when
				global $woocommerce;
				$order = wc_create_order(['status' => 'processing']);

				// then
				$mock->shouldHaveReceived('publish')->with(\Mockery::on(function ($opts) {
					global $test_topic;
					return $opts['TopicArn'] === $test_topic;
				}));
		}

		public function testPublishToTopicWhenOrderIsShipped()
		{
				// given
				global $test_topic;
				$test_topic = 'arn:aws:sns:123:TestTopic';
				$hooks = Hooks::instance();
				$hooks->settings->settings['topic_order_shipped'] = $test_topic;

				$mock = spy(SnsClient::class);
				$hooks->client = $mock;

				// when
				global $woocommerce;
				$order = wc_create_order(['status' => 'completed']);

				// then
				$mock->shouldHaveReceived('publish')->with(\Mockery::on(function ($opts) {
					global $test_topic;
					return $opts['TopicArn'] === $test_topic;
				}));
		}

		public function testPublishToTopicWhenOrderIsRefunded()
		{
				// given
				global $test_topic;
				$test_topic = 'arn:aws:sns:123:TestTopic';
				$hooks = Hooks::instance();
				$hooks->settings->settings['topic_order_refunded'] = $test_topic;

				$mock = spy(SnsClient::class);
				$hooks->client = $mock;

				// when
				global $woocommerce;
				$order = wc_create_order(['status' => 'refunded']);

				// then
				$mock->shouldHaveReceived('publish')->with(\Mockery::on(function ($opts) {
					global $test_topic;
					return $opts['TopicArn'] === $test_topic;
				}));
		}
}
