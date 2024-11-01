<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Modules;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Qodax\WcLiqPayPayments\Blocks\LiqPayBlockAdapter;
use Qodax\WcLiqPayPayments\Component\QodaxLiqPayPaymentGateway;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;

class PaymentMethod extends AbstractModule
{
    public function boot(): void
    {
        add_filter('woocommerce_payment_gateways', [$this, 'registerPaymentGateway']);
        add_action('woocommerce_blocks_loaded', [$this, 'addBlockSupport']);
    }

    /**
     * @param string[] $methods
     * @return string[]
     */
    public function registerPaymentGateway(array $methods): array
    {
        $methods[] = QodaxLiqPayPaymentGateway::class;

        return $methods;
    }

    public function addBlockSupport(): void
    {
        // Checking for old versions
        if( ! class_exists(AbstractPaymentMethodType::class)) {
            return;
        }

        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function(PaymentMethodRegistry $paymentMethodRegistry ) {
                $paymentMethodRegistry->register(new LiqPayBlockAdapter());
            }
        );
    }
}
