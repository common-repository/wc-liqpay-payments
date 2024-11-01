<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http\Middleware;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http\Request;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
class VerifyCsrfToken
{
    private const CSRF_TOKEN_NAME = '_nonce';
    public function handle(Request $request)
    {
        if (!wp_verify_nonce($request->get(self::CSRF_TOKEN_NAME, ''), Plugin::config()->get('plugin_id'))) {
            wp_send_json(['errors' => ['CSRF token mismatch']], 400);
        }
    }
}
