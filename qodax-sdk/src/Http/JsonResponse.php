<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Http\HttpResponseInterface;
class JsonResponse implements HttpResponseInterface
{
    /**
     * @var array
     */
    private $data;
    /**
     * JsonResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function send()
    {
        wp_send_json($this->data);
    }
}
