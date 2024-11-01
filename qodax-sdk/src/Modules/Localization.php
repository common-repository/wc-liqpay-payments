<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
class Localization extends AbstractModule
{
    public function boot() : void
    {
        add_action('plugins_loaded', [$this, 'loadPluginTextDomain']);
    }
    public function loadPluginTextDomain()
    {
        load_plugin_textdomain('wc-liqpay-payments-sdk', false, Plugin::config()->get('plugin_id') . '/qodax-sdk/lang');
    }
}
