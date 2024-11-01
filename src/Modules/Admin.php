<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Modules;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;

class Admin extends AbstractModule
{
    public function boot(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'loadAdminScripts']);
    }

    public function loadAdminScripts(): void
    {
        wp_enqueue_style(
            QODAX_WC_LIQPAY_PREFIX . 'admin_css',
            QODAX_WC_LIQPAY_PLUGIN_URL . 'assets/css/admin.min.css',
            [],
            filemtime(QODAX_WC_LIQPAY_PLUGIN_DIR . 'assets/css/admin.min.css')
        );
    }
}
