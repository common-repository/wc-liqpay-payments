<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers\HooksHelper;
class OptionsHelper
{
    public static function updateOption(string $key, $value) : bool
    {
        return update_option(self::prepareOptionKey($key), $value);
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOption(string $key, $default = null)
    {
        return get_option(self::prepareOptionKey($key), $default);
    }
    private static function prepareOptionKey(string $key) : string
    {
        $prefix = Plugin::config()->get('option_prefix');
        return apply_filters(HooksHelper::getHookName('fw_option_key'), $prefix . $key, $key);
    }
}
