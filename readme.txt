=== AWS Event Producer for WooCommerce ===
Contributors: Toriverkosto, Zuige
Tags: woocommerce, s3, sns, sqs, firehose, stream, aws, data
Donate link: https://github.com/anttiviljami
Requires at least: 4.7
Tested up to: 5.4
Requires PHP: 7.1
Stable tag: 1.2.1
License: GPLv3
License URI: https://github.com/Toriverkosto/woocommerce-aws-integration/blob/master/LICENSE

== Description ==

WooCommerce extension to publish events to AWS services from WooCommerce hooks.

You can configure this plugin to publish your WooCommerce business events to any
of the following AWS target services using their ARN:

- SNS Topic
- SQS Queue
- Kinesis Data Stream
- Firehose Delivery Stream
- S3 Bucket

The following events are currently supported out-of-the-box:

- Order Paid
- Order Shipped
- Order Refunded
- Product Published
- Product Sold
- Product Shipped
- Product Refunded

**Why**

This extension unlocks the power of the AWS ecosystem for your WooCommerce
store by pushing your important business events to AWS services.

Some example use cases:

- Send a notification with SNS for new orders
- Run custom Lambda functions for order events
- Query your order data using Athena and S3
- Analyze and visualize your store data using QuickSight
- Create a delivery queue for digital products using SQS
- Synchronize orders and product inventory to backend systems
- Archive your store order history to S3

**Contributing**

Please contribute to this project on Github. Pull requests welcome!

https://github.com/Toriverkosto/woocommerce-aws-integration

== Installation ==

Requirements:

- WooCommerce >= 3.1
- WordPress >= 4.7
- PHP >= 7.1

1. Install the latest version of this plugin and activate it.
2. Navigate to WooCommerce > Settings > Integration > AWS SNS Integration
3. Input the ARNs for the AWS resources you want to publish events to
4. If running outside of the AWS environment, you'll also need to configure IAM
Access keys to be able to access AWS Services

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Configuration screen

== Changelog ==

Commit log is available at
https://github.com/Toriverkosto/woocommerce-aws-integration/commits/master

== Upgrade Notice ==

* 1.0 There's an update available to AWS SNS Publisher for WooCommerce that
makes it better. Please upgrade to the newest version

