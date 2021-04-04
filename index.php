<?php
/**
 * Plugin Name: MesinKira Payment for WooCommerce
 * Plugin URI: https://pg.mesinkira.io/
 * Description: Integrate your WooCommerce site with MesinKira Payment Gateway.
 * Version: 1.0.0
 * Author: MesinKira
 * Author URI: https://pg.mesinkira.io/
 * tested up to: 5.5.1
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include toyyibPay Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'mkirapay_init', 0 );

function mkirapay_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/mkirapay.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_mkirapay_to_woocommerce' );
	function add_mkirapay_to_woocommerce( $methods ) {
		$methods[] = 'mkirapay';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mkirapay_links' );

function mkirapay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=mkirapay' ) . '">' . __( 'Settings', 'mkirapay' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

function requery_mkirapay($BillCode, $OrderId) {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	include_once( 'src/mkirapay.php' );

	$mkirapay = new mkirapay();
	$mkirapay->cron_requery($BillCode, $OrderId);
}

add_action( 'init', 'mkirapay_check_response', 15 );

function mkirapay_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/mkirapay.php' );

	$mkirapay = new mkirapay();
	$mkirapay->check_mkirapay_response();
	$mkirapay->check_mkirapay_callback();
	
}
