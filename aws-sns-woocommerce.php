<?php
/**
 * Plugin name: AWS SNS Producer for WooCommerce
 * Plugin URI: https://github.com/Toriverkosto/aws-sns-woocommerce
 * Description: A WordPress plugin to produce SNS events from WooCommerce hooks.
 * Version: 0.0.1
 * Author: @anttiviljami
 * Author: https://github.com/anttiviljami
 * License: GPLv3
 * Text Domain: aws-sns-woocommerce
 * Tested up to: 5.4
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

require 'vendor/autoload.php';
require 'lib/plugin.php';
