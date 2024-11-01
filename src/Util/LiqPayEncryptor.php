<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Util;

class LiqPayEncryptor
{
    private string $privateKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function encodeData(array $data): string
    {
        return base64_encode(wp_json_encode($data));
    }

    public function decodeData(string $encodedString): array
    {
        return json_decode(base64_decode($encodedString), true);
    }

    public function generateSignature(array $data): string
    {
        return base64_encode(
            sha1($this->privateKey . $this->encodeData($data) . $this->privateKey, true)
        );
    }

    public function generateSignatureFromRaw(string $data): string
    {
        return base64_encode(
            sha1($this->privateKey . $data . $this->privateKey, true)
        );
    }
}
