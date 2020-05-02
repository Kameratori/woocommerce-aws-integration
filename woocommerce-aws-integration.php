<?php
/**
 * Plugin name: AWS Event Producer for WooCommerce
 * Plugin URI: https://github.com/Toriverkosto/woocommerce-aws-integration
 * Description: WooCommerce extension to publish events to AWS services from WooCommerce hooks
 * Version: 1.2.1
 * Author: Toriverkosto
 * Author: https://github.com/Toriverkosto
 * License: GPLv3
 * Text Domain: woocommerce-aws-integration
 *
 * Copyright 2020 Viljami Kuosmanen
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( is_file( __DIR__ . '/vendor/autoload.php' ) === true ) {
	// wp zip installation
	require __DIR__ . '/vendor/autoload.php';
} else {
	// composer installation
	require 'vendor/autoload.php';
}

require __DIR__ . '/lib/Plugin.php';
