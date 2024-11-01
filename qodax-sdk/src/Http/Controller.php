<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http\HttpResponseInterface;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http\JsonResponse;
abstract class Controller
{
    public function json(array $data) : HttpResponseInterface
    {
        return new JsonResponse($data);
    }
}
