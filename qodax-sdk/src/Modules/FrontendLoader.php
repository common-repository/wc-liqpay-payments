<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers\HooksHelper;
class FrontendLoader extends AbstractModule
{
    public function boot() : void
    {
        add_action(HooksHelper::getHookName('fw_frontend_loaded'), [$this, 'registerScripts']);
    }
    public function registerScripts(string $fwScriptId) : void
    {
        $nonceKey = Plugin::config()->get('plugin_id');
        $globals = ['homeUrl' => home_url(), 'ajaxUrl' => admin_url('admin-ajax.php'), '_nonce' => wp_create_nonce($nonceKey), 'uiPrefix' => Plugin::config()->get('ui_prefix'), 'ajaxPrefix' => Plugin::config()->get('ajax_prefix'), 'lang' => $this->detectLanguage(), '_state' => apply_filters(HooksHelper::getHookName('fw_frontend_state'), [])];
        $globals = apply_filters(HooksHelper::getHookName('fw_frontend_globals'), $globals);
        wp_localize_script($fwScriptId, Plugin::config()->get('global_prefix') . 'fw_globals', $globals);
    }
    private function detectLanguage() : string
    {
        return \preg_replace('/_.+$/', '', get_locale());
    }
}
