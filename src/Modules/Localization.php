<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Modules;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;

class Localization extends AbstractModule
{
    public function boot(): void
    {
        add_action('plugins_loaded', [ $this, 'loadPluginTextDomain' ]);
    }

    public function loadPluginTextDomain()
    {
        load_plugin_textdomain('wc-liqpay-payments', false, dirname(QODAX_WC_LIQPAY_PLUGIN_NAME) . '/lang');
    }
}
