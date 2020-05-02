<?php namespace AWSWooCommerce;

interface IEvent {
	public function __construct( $target, $event, $data, $timestamp );
	public function publish();
}
