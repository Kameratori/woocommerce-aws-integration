=== AWS SNS Producer For WooCommerce ===
Contributors: Zuige
Tags: woocommerce, sns, aws, data
Donate link: https://github.com/anttiviljami
Requires at least: 4.7
Tested up to: 5.4
Requires PHP: 7.1
Stable tag: 1.0.0
License: GPLv3
License URI: https://github.com/Toriverkosto/aws-sns-woocommerce/blob/master/LICENSE

== Description ==

A WordPress plugin to produce SNS events from WooCommerce hooks.

You can configure this plugin to publish metadata from any SNS Topic triggered
by the following WooCommerce events:

- Order Paid
- Order Shipped
- Order Refunded
- Product Published
- Product Sold
- Product Shipped
- Product Refunded

**Contributing**

Please contribute to this project on Github. Pull requests welcome!

https://github.com/Toriverkosto/aws-sns-woocommerce

== Installation ==

Requirements:

- WooCommerce >= 3.1
- WordPress >= 4.7
- PHP >= 7.1

1. Install the latest version of this plugin and activate it.
2. Navigate to WooCommerce > Settings > Integration > AWS SNS Integration
3. Input the ARNs for the SNS topics you want to publish events on
4. If running outside of the AWS environment, you'll also need to configure IAM
Access keys

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Configuration screen

== Changelog ==

Commit log is available at
https://github.com/Toriverkosto/aws-sns-woocommerce/commits/master

== Upgrade Notice ==

* 1.0 There's an update available to AWS SNS Publisher for WooCommerce that
makes it better. Please upgrade to the newest version

