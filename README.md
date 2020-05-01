# AWS SNS Producer for WooCommerce

[![CI](https://github.com/Toriverkosto/aws-sns-woocommerce/workflows/CI/badge.svg)](https://github.com/Toriverkosto/aws-sns-woocommerce/actions?query=workflow%3ACI)
[![License](http://img.shields.io/:license-gpl3-blue.svg)](https://github.com/anttiviljami/wp-safe-updates/blob/master/LICENSE)

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

Download and install the [latest release](https://github.com/Toriverkosto/aws-sns-woocommerce/releases) of this plugin.

Navigate to WooCommerce > Settings >

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
