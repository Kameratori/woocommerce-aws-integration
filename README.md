# AWS SNS Producer for WooCommerce

[![CI](https://github.com/Toriverkosto/aws-sns-woocommerce/workflows/CI/badge.svg)](https://github.com/Toriverkosto/aws-sns-woocommerce/actions?query=workflow%3ACI)
[![License](https://img.shields.io/:license-gpl3-blue.svg)](https://github.com/anttiviljami/wp-safe-updates/blob/master/LICENSE)

A WordPress plugin to produce SNS events from WooCommerce hooks.

You can configure this plugin to publish metadata from any SNS Topic triggered by the following WooCommerce events:

- Order Paid
- Order Shipped
- Order Refunded
- Product Published
- Product Sold
- Product Shipped
- Product Refunded

## Installation

1. Download and install the [latest release](https://github.com/Toriverkosto/aws-sns-woocommerce/releases) of this plugin.

1. Navigate to WooCommerce > Settings > Integration > AWS SNS Integration

1. Input the ARNs for the SNS topics you want to publish events on

1. If running outside of a native AWS environment, you'll also need to configure
   IAM Access keys to be able to publish to AWS SNS Topics.

![Settings page](assets/screenshot-1.png)

## Development

Requirements:

- PHP 7.1+
- Composer
- Docker

```
composer install
composer run dev
```

To run tests:

```
composer run test
```
