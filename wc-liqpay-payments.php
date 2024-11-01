<?php
/**
 * Plugin Name: WC LiqPay Payments
 * Plugin URI: https://kirillbdev.pro/wc-liqpay-payments/
 * Description: Plugin that adds supporting of LiqPay payment gateway to your WooCommerce store.
 * Version: 1.1.1
 * Author: kirillbdev
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 * Tested up to: 6.6
 * WC tested up to: 9.1
*/

if ( ! defined('ABSPATH')) {
    exit;
}

include_once __DIR__ . '/vendor/autoload.php';

if (defined('QODAX_WC_LIQPAY_VERSION')) {
    // The user is attempting to activate a second plugin instance, typically Free and Pro versions.
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-includes/pluggable.php';
    if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate this plugin.
        // WP does not allow us to send a custom meaningful message, so just tell the plugin has been deactivated.
        wp_safe_redirect( add_query_arg( 'deactivate', 'true', remove_query_arg( 'activate' ) ) );
        exit;
    }
} else {
    define('QODAX_WC_LIQPAY_ROOT_FILE', __FILE__);
    define('QODAX_WC_LIQPAY_PLUGIN_NAME', plugin_basename(QODAX_WC_LIQPAY_ROOT_FILE));
    define('QODAX_WC_LIQPAY_PLUGIN_URL', plugin_dir_url(QODAX_WC_LIQPAY_ROOT_FILE));
    define('QODAX_WC_LIQPAY_PLUGIN_DIR', plugin_dir_path(QODAX_WC_LIQPAY_ROOT_FILE));
    define('QODAX_WC_LIQPAY_VERSION', '1.1.1');
    define('QODAX_WC_LIQPAY_PREFIX', 'qxwlp_');

    add_action( 'before_woocommerce_init', function() {
        if (class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    });

    Qodax\WcLiqPayPayments\WcLiqPayPayments::getInstance();
}
