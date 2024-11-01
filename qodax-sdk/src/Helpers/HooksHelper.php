<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
class HooksHelper
{
    private const HOOK_PREFIX_FRAMEWORK = 'fw_';
    public static function getHookName(string $hook) : string
    {
        return Plugin::config()->get('global_prefix') . $hook;
    }
}
