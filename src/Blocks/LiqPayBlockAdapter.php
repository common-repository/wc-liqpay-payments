<?php

namespace Qodax\WcLiqPayPayments\Blocks;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Qodax\WcLiqPayPayments\Component\QodaxLiqPayPaymentGateway;

final class LiqPayBlockAdapter extends AbstractPaymentMethodType
{
    protected $name = 'wc_liqpay_payments';

    private QodaxLiqPayPaymentGateway $gateway;

    public function initialize() {
        // get payment gateway settings
        $this->settings = get_option("woocommerce_{$this->name}_settings", []);

        $gateways = WC()->payment_gateways->payment_gateways();
        $this->gateway = $gateways[ $this->name ];
    }

    public function is_active(): bool
    {
        return $this->gateway->is_available();
    }

    /**
     * @return string[]
     */
    public function get_payment_method_script_handles(): array
    {
        wp_register_script(
            QODAX_WC_LIQPAY_PREFIX . 'blocks_js',
            QODAX_WC_LIQPAY_PLUGIN_URL . 'assets/js/blocks.min.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
            ],
            filemtime(QODAX_WC_LIQPAY_PLUGIN_DIR . 'assets/js/blocks.min.js'),
            true
        );

        return [
            QODAX_WC_LIQPAY_PREFIX . 'blocks_js',
        ];
    }

    public function get_payment_method_data()
    {
        return [
            'title' => $this->get_setting('title'),
            'description' => $this->get_setting('description'),
            'icon' => $this->get_setting('icon'), // not using now
        ];
    }
}
