<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
class UiHelper
{
    /**
     * @param string|string[] $class
     * @return string
     */
    public static function escClass($class) : string
    {
        if (!\is_array($class)) {
            $class = [$class];
        }
        $result = [];
        foreach ($class as $c) {
            $result[] = Plugin::config()->get('ui_prefix') . $c;
        }
        return esc_attr(\implode(' ', $result));
    }
    public static function renderSvg(string $path) : string
    {
        $fullPath = Plugin::config()->get('framework_path') . '/assets/image/' . $path;
        return \file_get_contents($fullPath);
    }
}
