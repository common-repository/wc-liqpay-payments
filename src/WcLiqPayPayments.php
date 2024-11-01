<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\Container;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\PluginConfig;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;

class WcLiqPayPayments
{
    private static ?WcLiqPayPayments $instance = null;

    private Plugin $plugin;

    public function __construct()
    {
        $modulesDir = QODAX_WC_LIQPAY_PLUGIN_DIR . 'modules';
        $loaders = glob($modulesDir . '/**/bootstrap.php');
        foreach ($loaders as $loader) {
            require_once $loader;
        }

        $this->plugin = Plugin::instance(
            new PluginConfig([
                'plugin_id' => dirname(QODAX_WC_LIQPAY_PLUGIN_NAME),
                'plugin_name' => 'WC LiqPay Payments',
                'plugin_version' => QODAX_WC_LIQPAY_VERSION,
                'global_prefix' => QODAX_WC_LIQPAY_PREFIX,
                'framework_path' => QODAX_WC_LIQPAY_PLUGIN_DIR . 'qodax-sdk',
                'framework_js_obj' => 'QodaxLiqPay',
            ]),
            $this->getDependencies(),
            $this->getModules(),
            QODAX_WC_LIQPAY_PLUGIN_DIR . 'views'
        );
        $this->plugin->init();
    }

    public static function getInstance(): WcLiqPayPayments
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getContainer(): Container
    {
        return $this->plugin->getContainer();
    }

    private function getDependencies(): array
    {
        return [];
    }

    private function getModules(): array
    {
        $modules = [
            \Qodax\WcLiqPayPayments\Modules\Localization::class,
            \Qodax\WcLiqPayPayments\Modules\Admin::class,
            \Qodax\WcLiqPayPayments\Modules\PaymentMethod::class,
        ];

        return apply_filters(QODAX_WC_LIQPAY_PREFIX . 'plugin_modules', $modules);
    }
}
